<?php

namespace eCamp\Api\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SwaggerControllerFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $config = $container->get('Config');
        $router = $container->get('Router');

        return new SwaggerController($config, $router);
    }
}
