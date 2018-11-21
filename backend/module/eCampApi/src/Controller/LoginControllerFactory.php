<?php

namespace eCamp\Api\Controller;

use eCamp\Core\EntityService\UserService;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

class LoginControllerFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $container->get(AuthenticationService::class);

        /** @var UserService $userService */
        $userService = $container->get(UserService::class);

        return new LoginController($authenticationService, $userService);
    }
}
