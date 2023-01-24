<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\Invitation;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

class InvitationProvider implements ProviderInterface {
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
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Invitation {
        $id = $uriVariables['inviteKey'];
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
            $identifier = $this->security->getUser()->getUserIdentifier();

            /** @var User $user */
            $user = $this->userRepository->loadUserByIdentifier($identifier);
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
}
