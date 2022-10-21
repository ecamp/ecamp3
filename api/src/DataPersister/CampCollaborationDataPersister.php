<?php

namespace App\DataPersister;

use ApiPlatform\Validator\ValidatorInterface;
use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\CustomActionListener;
use App\DataPersister\Util\DataPersisterObservable;
use App\DataPersister\Util\PropertyChangeListener;
use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Service\MailService;
use App\Util\IdGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

class CampCollaborationDataPersister extends AbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private Security $security,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private ProfileRepository $profileRepository,
        private EntityManagerInterface $em,
        private MailService $mailService,
        private ValidatorInterface $validator
    ) {
        $resendInvitationListener = CustomActionListener::of(
            CampCollaboration::RESEND_INVITATION,
            fn (CampCollaboration $data) => $this->onBeforeResendInvitation($data),
            fn (CampCollaboration $data) => $this->onAfterResendInvitation($data)
        );
        $statusChangeListener = PropertyChangeListener::of(
            extractProperty: fn (CampCollaboration $data) => $data->status,
            beforeAction: fn (CampCollaboration $data) => $this->onBeforeStatusChange($data),
            afterAction: fn (CampCollaboration $data) => $this->onAfterStatusChange($data)
        );
        parent::__construct(
            CampCollaboration::class,
            $dataPersisterObservable,
            customActionListeners: [$resendInvitationListener],
            propertyChangeListeners: [$statusChangeListener]
        );
    }

    /**
     * @param CampCollaboration $data
     */
    public function beforeCreate($data): CampCollaboration {
        /** @var CampCollaboration $data */
        $inviteEmail = $data->user?->getEmail() ?? $data->inviteEmail;
        if (CampCollaboration::STATUS_INVITED == $data->status && $inviteEmail) {
            $profileByInviteEmail = $this->profileRepository->findOneBy(['email' => $inviteEmail]);
            if (null != $profileByInviteEmail) {
                $data->user = $profileByInviteEmail->user;
                $data->inviteEmail = null;
                $this->validator->validate($data, ['groups' => ['Default', 'create']]);
            }
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
            $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        }

        return $data;
    }

    /**
     * @param CampCollaboration $data
     */
    public function afterCreate($data): void {
        $this->sendInviteEmail($data);

        $materialList = new MaterialList();
        $materialList->campCollaboration = $data;
        $data->camp->addMaterialList($materialList);
        $this->em->persist($materialList);

        $this->em->flush();
    }

    public function onBeforeStatusChange(CampCollaboration $data): CampCollaboration {
        if (CampCollaboration::STATUS_INVITED == $data->status && ($data->inviteEmail || $data->user)) {
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
            $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        }

        return $data;
    }

    public function onAfterStatusChange(CampCollaboration $data): void {
        $this->sendInviteEmail($data);
    }

    public function onBeforeResendInvitation(CampCollaboration $data) {
        /** @var User $user */
        $user = $this->security->getUser();
        $data->inviteKey = IdGenerator::generateRandomHexString(64);
        $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);

        return $data;
    }

    public function onAfterResendInvitation(CampCollaboration $data) {
        $this->sendInviteEmail($data);
    }

    private function sendInviteEmail(CampCollaboration $data) {
        if (CampCollaboration::STATUS_INVITED == $data->status && $data->getEmail()) {
            /** @var User $user */
            $user = $this->security->getUser();

            $this->mailService->sendInviteToCampMail($user, $data->camp, $data->inviteKey, $data->getEmail());
        }
    }
}
