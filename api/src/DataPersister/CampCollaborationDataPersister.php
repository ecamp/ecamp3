<?php

namespace App\DataPersister;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\CustomActionListener;
use App\DataPersister\Util\DataPersisterObservable;
use App\DataPersister\Util\PropertyChangeListener;
use App\Entity\BaseEntity;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Util\IdGenerator;
use Symfony\Component\Security\Core\Security;

class CampCollaborationDataPersister extends AbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private Security $security,
        private UserRepository $userRepository,
        private MailService $mailService,
        private ValidatorInterface $validator
    ) {
        $resendInvitationListener = CustomActionListener::of(
            CampCollaboration::RESEND_INVITATION,
            afterAction: fn (CampCollaboration $data) => $this->onResendInvitation($data)
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

    public function beforeCreate($data): BaseEntity {
        /** @var CampCollaboration $data */
        $inviteEmail = $data->user?->email ?? $data->inviteEmail;
        if (CampCollaboration::STATUS_INVITED == $data->status && $inviteEmail) {
            $userByInviteEmail = $this->userRepository->findOneBy(['email' => $inviteEmail]);
            if (null != $userByInviteEmail) {
                $data->user = $userByInviteEmail;
                $data->inviteEmail = null;
                $this->validator->validate($data, ['groups' => ['Default', 'create']]);
            }
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
        }

        return $data;
    }

    public function afterCreate($data): void {
        /** @var User $user */
        $user = $this->security->getUser();
        $emailToInvite = $data->user?->email ?? $data->inviteEmail;
        if (CampCollaboration::STATUS_INVITED == $data->status && $emailToInvite) {
            $this->mailService->sendInviteToCampMail($user, $data->camp, $data->inviteKey, $emailToInvite);
        }
    }

    public function beforeRemove($data): ?BaseEntity {
        $this->validator->validate($data, ['groups' => ['delete']]);

        return null;
    }

    public function onBeforeStatusChange(CampCollaboration $data): CampCollaboration {
        if (CampCollaboration::STATUS_INVITED == $data->status && ($data->inviteEmail || $data->user)) {
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
        }

        return $data;
    }

    public function onAfterStatusChange(CampCollaboration $data): void {
        /** @var User $user */
        $user = $this->security->getUser();
        if (CampCollaboration::STATUS_INVITED == $data->status && ($data->inviteEmail || $data->user)) {
            $campCollaborationUser = $data->user;
            $inviteEmail = $data->inviteEmail;
            if (null != $campCollaborationUser) {
                $inviteEmail = $campCollaborationUser->email;
            }
            $this->mailService->sendInviteToCampMail(
                $user,
                $data->camp,
                $data->inviteKey,
                $inviteEmail
            );
        }
    }

    public function onResendInvitation(CampCollaboration $data) {
        /** @var User $user */
        $user = $this->security->getUser();
        $this->mailService->sendInviteToCampMail($user, $data->camp, $data->inviteKey, $data->inviteEmail);
    }
}
