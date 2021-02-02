<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;
use Laminas\Authentication\AuthenticationService;

abstract class HitobitoController extends BaseController {
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
            $this->getProviderName(),
            $hybridAuthConfig
        );
    }

    abstract protected function getProviderName(): string;
}
