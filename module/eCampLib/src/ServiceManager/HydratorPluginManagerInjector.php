<?php

namespace eCamp\Lib\ServiceManager;

use Interop\Container\ContainerInterface;
use ProxyManager\Proxy\LazyLoadingInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Initializer\InitializerInterface;

class HydratorPluginManagerInjector implements InitializerInterface {
    public function __invoke(ContainerInterface $container, $instance) {
        self::Inject($container, $instance);
    }

    public static function Inject(ContainerInterface $container, $instance) {
        if ($instance instanceof LazyLoadingInterface && !$instance->isProxyInitialized()) {
            return;
        }

        if ($instance instanceof HydratorPluginManagerAware) {
            /** @var HydratorPluginManager $hydratorPluginManager */
            $hydratorPluginManager = $container->get(HydratorPluginManager::class);
            $instance->setHydratorPluginManager($hydratorPluginManager);
        }
    }
}
