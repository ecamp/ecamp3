<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Repository\CampCollaborationRepository;
use Symfony\Component\Security\Core\Security;

class InvitationDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(
        private ContextAwareDataPersisterInterface $dataPersister,
        private CampCollaborationRepository $campCollaborationRepository,
        private Security $security
    ) {
    }

    public function supports($data, array $context = []): bool {
        return $data instanceof Invitation;
    }

    /**
     * @param Invitation $data
     */
    public function persist($data, array $context = []) {
        if (Invitation::ACCEPT === ($context['item_operation_name'] ?? null)) {
            $campCollaboration = $this->campCollaborationRepository->findByInviteKey($data->inviteKey);
            $campCollaboration->user = $this->security->getUser();
            $campCollaboration->status = CampCollaboration::STATUS_ESTABLISHED;
            $campCollaboration->inviteKey = null;
            $campCollaboration->inviteEmail = null;
            //TODO: add MaterialList for User
            $this->dataPersister->persist($campCollaboration);

            $data->userAlreadyInCamp = true;
        } elseif (Invitation::REJECT === ($context['item_operation_name'] ?? null)) {
            $campCollaboration = $this->campCollaborationRepository->findByInviteKey($data->inviteKey);
            $campCollaboration->user = $this->security->getUser();
            $campCollaboration->status = CampCollaboration::STATUS_INACTIVE;
            $campCollaboration->inviteKey = null;
            $this->dataPersister->persist($campCollaboration);
        }
    }

    public function remove($data, array $context = []) {
        throw new \RuntimeException('remove is not supported for '.Invitation::class);
    }
}
