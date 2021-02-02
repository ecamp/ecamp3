<?php

namespace eCamp\Core\Auth;

use eCamp\Core\Entity\User;
use eCamp\Core\Repository\UserRepository;
use Laminas\Authentication\AuthenticationService;

class AuthUserProvider {
    private UserRepository $userRepository;
    private AuthenticationService $authenticationService;

    public function __construct(UserRepository $userRepository, AuthenticationService $authenticationService) {
        $this->userRepository = $userRepository;
        $this->authenticationService = $authenticationService;
    }

    public function getAuthUser(): ?User {
        if ($this->authenticationService->hasIdentity()) {
            $userId = $this->authenticationService->getIdentity();

            return $this->userRepository->find($userId);
        }

        return null;
    }
}
