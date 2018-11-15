<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Auth\AuthService;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;

class GoogleController extends BaseController {
    public function __construct(
        EntityManager $entityManager,
        UserIdentityService $userIdentityService,
        UserService $userService,
        AuthService $authService,
        $cryptoKey
    ) {
        parent::__construct(
            $entityManager,
            $userIdentityService,
            $userService,
            $authService,
            'Google',
            $cryptoKey
        );
    }

    protected function getCallbackRoute() {
        return 'ecamp.auth/google';
    }
}
