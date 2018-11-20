<?php

namespace eCamp\Core\Auth;

use eCamp\Core\Entity\User;
use eCamp\Core\Repository\UserRepository;
use Zend\Authentication\AuthenticationService;

class AuthUserProvider {
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /** @return null|User */
    public function getAuthUser() {
        $authenticationService = new AuthenticationService();
        if ($authenticationService->hasIdentity()) {
            $userId = $authenticationService->getIdentity();

            /** @var User $user */
            $user = $this->userRepository->find($userId);

            return $user;
        }

        return null;
    }
}
