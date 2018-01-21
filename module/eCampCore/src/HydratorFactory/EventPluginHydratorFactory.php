<?php

namespace eCamp\Core\HydratorFactory;

use eCamp\Core\Hydrator\EventPluginHydrator;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class EventPluginHydratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new EventPluginHydrator($container);
    }
}