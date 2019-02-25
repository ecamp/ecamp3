<?php

namespace eCamp\Api\Controller;

use eCamp\Core\Service\RegisterService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RegisterControllerFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var RegisterService $registerService */
        $registerService = $container->get(RegisterService::class);

        return new RegisterController($registerService);
    }
}