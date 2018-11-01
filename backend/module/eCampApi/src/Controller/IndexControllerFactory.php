<?php

namespace eCamp\Api\Controller;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\EntityService\UserService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var AuthService $authService */
        $authService = $container->get(AuthService::class);

        /** @var UserService $userService */
        $userService = $container->get(UserService::class);

        return new IndexController($authService, $userService);
    }
}
