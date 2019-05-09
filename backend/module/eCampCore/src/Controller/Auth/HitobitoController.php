<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;
use Zend\Authentication\AuthenticationService;

class HitobitoController extends BaseController {
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
          'Hitobito',
          $hybridAuthConfig
        );
    }

    protected function getCallbackUri($route = null, $params = [], $options = []) {
        //return 'urn:ietf:wg:oauth:2.0:oob';
        return 'https://localhost:4001/auth/hitobito/callback';
    }

    /** @return string */
    protected function getCallbackRoute() {
        return 'ecamp.auth/hitobito';
    }
}
