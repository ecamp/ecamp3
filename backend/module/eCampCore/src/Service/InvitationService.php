<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\MaterialListService;
use eCamp\Core\EntityService\UserService;
use eCamp\Core\Repository\CampCollaborationRepository;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class InvitationService {
    private AuthenticationService $authenticationService;
    private CampCollaborationRepository $campCollaborationRepository;
    private UserService $userService;
    private MaterialListService $materialListService;

    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService, UserService $userService, MaterialListService $materialListService) {
        $this->authenticationService = $authenticationService;
        /** @var CampCollaborationRepository $entityRepository */
        $entityRepository = $serviceUtils->emGetRepository(CampCollaboration::class);
        $this->campCollaborationRepository = $entityRepository;
        $this->userService = $userService;
        $this->materialListService = $materialListService;
    }

    /**
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws NonUniqueResultException
     */
    public function findInvitation(string $inviteKey): ?Invitation {
        $campCollaboration = $this->campCollaborationRepository->findByInviteKey($inviteKey);
        if (null == $campCollaboration) {
            return null;
        }
        $camp = $campCollaboration->getCamp();
        $userDisplayName = null;
        $userAlreadyInCamp = null;
        $userId = $this->authenticationService->getIdentity();
        if (null != $userId) {
            /** @var User $user */
            $user = $this->userService->fetch($userId);
            $userDisplayName = $user->getDisplayName();
            $existingCampCollaboration = $this->campCollaborationRepository->findByUserAndCamp($user, $camp);
            $userAlreadyInCamp = null != $existingCampCollaboration && $existingCampCollaboration->isEstablished();
        }

        return new Invitation($camp->getId(), $camp->getTitle(), $userDisplayName, $userAlreadyInCamp);
    }

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     * @throws NoAccessException
     * @throws EntityValidationException
     * @throws ORMException
     */
    public function acceptInvitation(string $inviteKey, string $userId): Invitation {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $this->campCollaborationRepository->findByInviteKey($inviteKey);
        if (null == $campCollaboration) {
            throw new EntityNotFoundException();
        }
        /** @var User $user */
        $user = $this->userService->fetch($userId);
        if (null == $user) {
            throw new EntityNotFoundException();
        }
        $camp = $campCollaboration->getCamp();
        $existingCampCollaboration = $this->campCollaborationRepository->findByUserAndCamp($user, $camp);
        if (null != $existingCampCollaboration && $existingCampCollaboration->isEstablished()) {
            throw (new EntityValidationException())->setMessages(
                [
                    'user' => [
                        'alreadyInCamp' => "The user {$user->getDisplayName()} is already associated with the camp {$camp->getTitle()}",
                    ],
                ]
            );
        }
        $campCollaboration->setUser($user);
        $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
        $campCollaboration->setInviteKey(null);
        $campCollaboration->setInviteEmail(null);
        $this->campCollaborationRepository->saveWithoutAcl($campCollaboration);
        if (in_array($campCollaboration->getRole(), [CampCollaboration::ROLE_MEMBER, CampCollaboration::ROLE_MANAGER])) {
            $this->materialListService->create((object) [
                'campId' => $campCollaboration->getCamp()->getId(),
                'name' => $campCollaboration->getUser()->getDisplayName(),
            ]);
        }

        return new Invitation($camp->getId(), $camp->getTitle(), $user->getDisplayName(), true);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function rejectInvitation(string $inviteKey): Invitation {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $this->campCollaborationRepository->findByInviteKey($inviteKey);
        if (null == $campCollaboration) {
            throw new EntityNotFoundException();
        }
        $campCollaboration->setStatus(CampCollaboration::STATUS_INACTIVE);
        $campCollaboration->setInviteKey(null);
        $this->campCollaborationRepository->saveWithoutAcl($campCollaboration);

        $camp = $campCollaboration->getCamp();

        return new Invitation($camp->getId(), $camp->getTitle(), null, null);
    }
}
