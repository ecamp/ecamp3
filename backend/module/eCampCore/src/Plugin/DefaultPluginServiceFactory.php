<?php

namespace eCamp\Core\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DefaultPluginServiceFactory extends BasePluginServiceFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $eventPluginId = $this->getEventPluginId($container);

        return new $requestedName($eventPluginId);
    }
}
