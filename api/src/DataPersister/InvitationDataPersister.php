<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\CustomActionListener;
use App\DataPersister\Util\DataPersisterObservable;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Repository\CampCollaborationRepository;
use Symfony\Component\Security\Core\Security;

class InvitationDataPersister extends AbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private CampCollaborationRepository $campCollaborationRepository,
        private Security $security
    ) {
        $acceptListener = CustomActionListener::of(Invitation::ACCEPT, fn ($data) => $this->onAccept($data));
        $rejectListener = CustomActionListener::of(Invitation::REJECT, fn ($data) => $this->onReject($data));
        parent::__construct(
            Invitation::class,
            $dataPersisterObservable,
            customActionListeners: [$acceptListener, $rejectListener]
        );
    }

    /**
     * Override supports to omit check if Invitation is a managed Entity (which it's not).
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof Invitation;
    }

    public function onAccept($data): CampCollaboration {
        $inviteKeyHash = md5($data->inviteKey);
        $campCollaboration = $this->campCollaborationRepository->findByInviteKeyHash($inviteKeyHash);
        $campCollaboration->user = $this->security->getUser();
        $campCollaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $campCollaboration->inviteKey = null;
        $campCollaboration->inviteEmail = null;

        return $campCollaboration;
    }

    public function onReject($data): CampCollaboration {
        $inviteKeyHash = md5($data->inviteKey);
        $campCollaboration = $this->campCollaborationRepository->findByInviteKeyHash($inviteKeyHash);
        $campCollaboration->status = CampCollaboration::STATUS_INACTIVE;
        $campCollaboration->inviteKey = null;

        return $campCollaboration;
    }

    /**
     * Override persist method to return Invitation instead of the persisted CampCollaboration.
     */
    public function persist($data, array $context = []) {
        parent::persist($data, $context);

        return $data;
    }
}
