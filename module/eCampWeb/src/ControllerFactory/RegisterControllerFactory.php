<?php

namespace eCamp\Web\ControllerFactory;

use eCamp\Core\Service\RegisterService;
use eCamp\Web\Controller\RegisterController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RegisterControllerFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var RegisterService $registerService */
        $registerService = $container->get(RegisterService::class);

        return new RegisterController($registerService);
    }
}
