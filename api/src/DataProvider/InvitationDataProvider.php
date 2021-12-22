<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\SerializerAwareDataProviderTrait;
use App\DTO\Invitation;
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

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Invitation {
        if (null == $id) {
            return null;
        }
        $campCollaboration = $this->campCollaborationRepository->findByInviteKey($id);
        if (null === $campCollaboration) {
            return null;
        }
        $userAlreadyInCamp = null;
        $userDisplayName = null;

        $camp = $campCollaboration->camp;
        if (null !== $this->security->getUser()) {
            $username = $this->security->getUser()->getUserIdentifier();
            $user = $this->userRepository->findOneBy(['username' => $username]);
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
