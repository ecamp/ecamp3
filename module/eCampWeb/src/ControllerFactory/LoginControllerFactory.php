<?php

namespace eCamp\Web\ControllerFactory;

use eCamp\Core\Auth\AuthService;
use eCamp\Web\Controller\LoginController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var AuthService $authService */
        $authService = $container->get(AuthService::class);

        return new LoginController($authService);
    }
}
