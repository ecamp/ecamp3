<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Repository\CampCollaborationRepository;
use App\State\Util\AbstractPersistProcessor;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class InvitationRejectProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private CampCollaborationRepository $campCollaborationRepository
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Invitation $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): CampCollaboration {
        $inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        $campCollaboration = $this->campCollaborationRepository->findByInviteKeyHash($inviteKeyHash);
        $campCollaboration->status = CampCollaboration::STATUS_INACTIVE;
        $campCollaboration->inviteKey = null;
        $campCollaboration->inviteKeyHash = null;

        return $campCollaboration;
    }

    /**
     * Override process method to return Invitation instead of the persisted CampCollaboration.
     *
     * @param Invitation $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []) {
        parent::process($data, $operation, $uriVariables, $context);

        return $data;
    }
}
