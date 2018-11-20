<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;
use Zend\Authentication\AuthenticationService;

class GoogleController extends BaseController {
    public function __construct(
        EntityManager $entityManager,
        UserIdentityService $userIdentityService,
        UserService $userService,
        AuthenticationService $authenticationService,
        array $hybridAuthConfig
    ) {
        parent::__construct(
            $entityManager,
            $userIdentityService,
            $userService,
            $authenticationService,
            'Google',
            $hybridAuthConfig
        );
    }

    protected function getCallbackRoute() {
        return 'ecamp.auth/google';
    }
}
