<?php

namespace eCamp\Lib\ServiceManager;

use Interop\Container\ContainerInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Initializer\InitializerInterface;

class HydratorPluginManagerInjector implements InitializerInterface
{

    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof HydratorPluginManagerAware) {
            /** @var HydratorPluginManager $hydratorPluginManager */
            $hydratorPluginManager = $container->get(HydratorPluginManager::class);
            $instance->setHydratorPluginManager($hydratorPluginManager);
        }
    }
}
