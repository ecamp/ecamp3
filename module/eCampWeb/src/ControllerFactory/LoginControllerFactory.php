<?php

namespace eCamp\Web\ControllerFactory;

use eCamp\Web\Controller\LoginController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new LoginController();
    }
}
