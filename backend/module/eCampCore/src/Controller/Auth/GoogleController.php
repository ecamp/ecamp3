<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;
use Laminas\Authentication\AuthenticationService;

class GoogleController extends BaseController {
    public function __construct(
        EntityManager $entityManager,
        UserIdentityService $userIdentityService,
        UserService $userService,
        AuthenticationService $laminasAuthenticationService,
        array $hybridAuthConfig
    ) {
        parent::__construct(
            $entityManager,
            $userIdentityService,
            $userService,
            $laminasAuthenticationService,
            'Google',
            $hybridAuthConfig
        );
    }

    protected function getCallbackRoute(): string {
        return 'ecamp.auth/google';
    }
}
