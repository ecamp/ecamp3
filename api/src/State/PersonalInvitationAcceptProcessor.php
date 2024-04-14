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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @implements ProcessorInterface<PersonalInvitation,PersonalInvitation>
 */
class PersonalInvitationAcceptProcessor implements ProcessorInterface {
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
        if (null === $campCollaboration) {
            throw new NoResultException();
        }

        $campCollaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $campCollaboration->inviteKey = null;
        $campCollaboration->inviteKeyHash = null;
        $campCollaboration->inviteEmail = null;

        $this->em->flush();

        return $data;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getUser(): ?User {
        $user = $this->security->getUser();
        if (null == $user) {
            // This should never happen because it should be caught earlier by our security settings
            // on all API operations using this processor.
            throw new AccessDeniedHttpException();
        }

        return $this->userRepository->loadUserByIdentifier($user->getUserIdentifier());
    }
}
