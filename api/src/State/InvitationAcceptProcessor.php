<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Repository\CampCollaborationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @implements ProcessorInterface<Invitation>
 */
class InvitationAcceptProcessor implements ProcessorInterface {
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private CampCollaborationRepository $campCollaborationRepository,
        private Security $security,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @param Invitation $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Invitation {
        $inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);

        $campCollaboration = $this->campCollaborationRepository->findByInviteKeyHash($inviteKeyHash);
        $campCollaboration->user = $this->security->getUser();
        $campCollaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $campCollaboration->inviteKey = null;
        $campCollaboration->inviteKeyHash = null;
        $campCollaboration->inviteEmail = null;

        $this->em->flush();

        return $data;
    }
}
