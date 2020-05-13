<?php

namespace eCamp\Core\Auth;

use eCamp\Core\Entity\User;
use eCamp\Core\Repository\UserRepository;
use Laminas\Authentication\AuthenticationService;

class AuthUserProvider {
    /** @var UserRepository */
    private $userRepository;

    /** @var AuthenticationService */
    private $authenticationService;

    public function __construct(UserRepository $userRepository, AuthenticationService $authenticationService) {
        $this->userRepository = $userRepository;
        $this->authenticationService = $authenticationService;
    }

    /** @return null|User */
    public function getAuthUser() {
        if ($this->authenticationService->hasIdentity()) {
            $userId = $this->authenticationService->getIdentity();

            // @var User $user
            return $this->userRepository->find($userId);
        }

        return null;
    }
}
