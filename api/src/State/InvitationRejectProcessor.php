<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Repository\CampCollaborationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class InvitationRejectProcessor implements ProcessorInterface {
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private CampCollaborationRepository $campCollaborationRepository,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @param Invitation $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Invitation {
        $inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);

        $campCollaboration = $this->campCollaborationRepository->findByInviteKeyHash($inviteKeyHash);
        $campCollaboration->status = CampCollaboration::STATUS_INACTIVE;
        $campCollaboration->inviteKey = null;
        $campCollaboration->inviteKeyHash = null;

        $this->em->flush();

        return $data;
    }
}
