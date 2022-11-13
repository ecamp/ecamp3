<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Repository\CampCollaborationRepository;
use App\State\Util\AbstractPersistProcessor;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

class InvitationAcceptProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private CampCollaborationRepository $campCollaborationRepository,
        private Security $security
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Invitation $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): CampCollaboration {
        $inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        $campCollaboration = $this->campCollaborationRepository->findByInviteKeyHash($inviteKeyHash);
        $campCollaboration->user = $this->security->getUser();
        $campCollaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $campCollaboration->inviteKey = null;
        $campCollaboration->inviteKeyHash = null;
        $campCollaboration->inviteEmail = null;

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
