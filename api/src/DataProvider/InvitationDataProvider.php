<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\SerializerAwareDataProviderTrait;
use App\DTO\Invitation;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

class InvitationDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    use SerializerAwareDataProviderTrait;

    public function __construct(
        private Security $security,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private UserRepository $userRepository,
        private CampCollaborationRepository $campCollaborationRepository
    ) {
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Invitation {
        if (null == $id) {
            return null;
        }

        $idHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($id);
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
