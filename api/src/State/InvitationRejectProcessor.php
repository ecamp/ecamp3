<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Repository\CampCollaborationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @implements ProcessorInterface<Invitation,Invitation>
 */
class InvitationRejectProcessor implements ProcessorInterface {
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
        private readonly CampCollaborationRepository $campCollaborationRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @param Invitation $data
     */
    #[\Override]
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
