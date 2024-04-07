<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\PersonalInvitation;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @template-implements ProviderInterface<PersonalInvitation>
 */
class PersonalInvitationProvider implements ProviderInterface {
    public function __construct(
        private readonly Security $security,
        private readonly UserRepository $userRepository,
        private readonly CampCollaborationRepository $campCollaborationRepository
    ) {}

    /**
     * @return null|PersonalInvitation|PersonalInvitation[]
     *
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): null|array|PersonalInvitation {
        if (isset($uriVariables['id'])) {
            $id = $uriVariables['id'];

            return $this->provideItem($id);
        }

        return $this->provideCollection();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function provideCollection(): array {
        $user = $this->getUser();
        if (null == $user) {
            return [];
        }
        $campCollaborations = $this->campCollaborationRepository->findAllByPersonallyInvitedUser($user);

        return array_map(function (CampCollaboration $campCollaboration) {
            return $this->toInvitation($campCollaboration);
        }, $campCollaborations);
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function provideItem(string $id): ?PersonalInvitation {
        $user = $this->getUser();
        if (null == $id || null == $user) {
            return null;
        }
        $campCollaboration = $this->campCollaborationRepository->findByUserAndIdAndInvited($user, $id);

        return $this->toInvitation($campCollaboration);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getUser(): ?User {
        $user = $this->security->getUser();
        if (null == $user) {
            return null;
        }

        return $this->userRepository->loadUserByIdentifier($user->getUserIdentifier());
    }

    private function toInvitation(?CampCollaboration $campCollaboration): ?PersonalInvitation {
        if (null == $campCollaboration) {
            return null;
        }

        $camp = $campCollaboration->camp;

        return new PersonalInvitation($campCollaboration->getId(), $camp->getId(), $camp->title);
    }
}
