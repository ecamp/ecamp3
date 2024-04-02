<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\PersonalInvitation;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProcessorInterface<PersonalInvitation,PersonalInvitation>
 */
class PersonalInvitationRejectProcessor implements ProcessorInterface {
    public function __construct(
        private readonly CampCollaborationRepository $campCollaborationRepository,
        private readonly UserRepository $userRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @param PersonalInvitation $data
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): PersonalInvitation {
        $user = $this->getUser();

        $campCollaboration = $this->campCollaborationRepository->findByUserAndIdAndInvited($user, $data->id);
        $campCollaboration->status = CampCollaboration::STATUS_INACTIVE;
        $campCollaboration->inviteKey = null;
        $campCollaboration->inviteKeyHash = null;

        $this->em->flush();

        return $data;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function getUser(): ?User {
        $user = $this->security->getUser();
        if (null == $user) {
            return null;
        }

        return $this->userRepository->loadUserByIdentifier($user->getUserIdentifier());
    }
}
