<?php

namespace eCampApi\V1\Rpc\Auth;

use eCamp\Core\EntityService\UserService;
use Psr\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;

class AuthControllerFactory {
    /**
     * @param ContainerInterface $controllers
     *
     * @return AuthController
     */
    public function __invoke($controllers) {
        $authenticationService = $controllers->get(AuthenticationService::class);

        /** @var UserService $userService */
        $userService = $controllers->get(UserService::class);

        return new AuthController($authenticationService, $userService);
    }
}
