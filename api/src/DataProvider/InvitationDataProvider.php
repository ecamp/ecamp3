<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\SerializerAwareDataProviderTrait;
use App\DTO\Invitation;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class InvitationDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    use SerializerAwareDataProviderTrait;
    private CampCollaborationRepository $campCollaborationRepository;
    private Security $security;
    private UserRepository $userRepository;

    public function __construct(Security $security, UserRepository $userRepository, CampCollaborationRepository $campCollaborationRepository) {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->campCollaborationRepository = $campCollaborationRepository;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Invitation {
        if (null == $id) {
            return null;
        }
        $idHash = md5($id);
        $campCollaboration = $this->campCollaborationRepository->findByInviteKeyHash($idHash);
        if (null === $campCollaboration) {
            return null;
        }
        $userAlreadyInCamp = null;
        $userDisplayName = null;

        $camp = $campCollaboration->camp;
        if (null !== $this->security->getUser()) {
            $username = $this->security->getUser()->getUserIdentifier();
            /** @var User $user */
            $user = $this->userRepository->loadUserByIdentifier($username);
            $userDisplayName = $user->getDisplayName();
            $existingCampCollaboration = $this->campCollaborationRepository->findByUserAndCamp($user, $camp);
            if ($existingCampCollaboration === $campCollaboration) {
                $userAlreadyInCamp = false;
            } else {
                $userAlreadyInCamp = null !== $existingCampCollaboration;
            }
        }

        return new Invitation($id, $camp->getId(), $camp->title, $userDisplayName, $userAlreadyInCamp);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
        return Invitation::class === $resourceClass;
    }
}
