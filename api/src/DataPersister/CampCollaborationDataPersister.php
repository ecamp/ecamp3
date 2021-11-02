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
            fn (CampCollaboration $data) => $data->status,
            fn (CampCollaboration $data) => $this->onStatusChange($data)
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
        if (CampCollaboration::STATUS_INVITED == $data->status && $data->inviteEmail) {
            $userByInviteEmail = $this->userRepository->findOneBy(['email' => $data->inviteEmail]);
            if (null != $userByInviteEmail) {
                $data->user = $userByInviteEmail;
            }
        }

        return $data;
    }

    public function afterCreate($data): void {
        /** @var User $user */
        $user = $this->security->getUser();
        if (CampCollaboration::STATUS_INVITED == $data->status && $data->inviteEmail) {
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
            $this->mailService->sendInviteToCampMail($user, $data->camp, $data->inviteKey, $data->inviteEmail);
        }
    }

    public function beforeRemove($data): ?BaseEntity {
        $this->validator->validate($data, ['groups' => ['delete']]);

        return null;
    }

    public function onStatusChange(CampCollaboration $data) {
        /** @var User $user */
        $user = $this->security->getUser();
        if (CampCollaboration::STATUS_INVITED == $data->status && ($data->inviteEmail || $data->user)) {
            $campCollaborationUser = $data->user;
            $inviteEmail = $data->inviteEmail;
            if (null != $campCollaborationUser) {
                $inviteEmail = $campCollaborationUser->email;
            }
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
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
