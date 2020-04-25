<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;
use Zend\Authentication\AuthenticationService;

abstract class HitobitoController extends BaseController {

    /** @return string */
    abstract protected function getProviderName();

    public function __construct(
        EntityManager $entityManager,
        UserIdentityService $userIdentityService,
        UserService $userService,
        AuthenticationService $zendAuthenticationService,
        array $hybridAuthConfig
    ) {
        parent::__construct(
            $entityManager,
            $userIdentityService,
            $userService,
            $zendAuthenticationService,
            $this->getProviderName(),
            $hybridAuthConfig
        );
    }
}
