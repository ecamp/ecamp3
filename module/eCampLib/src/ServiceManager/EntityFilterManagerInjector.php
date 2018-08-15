<?php

namespace eCamp\Lib\ServiceManager;

use Interop\Container\ContainerInterface;
use ProxyManager\Proxy\LazyLoadingInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class EntityFilterManagerInjector implements InitializerInterface {
    public function __invoke(ContainerInterface $container, $instance) {
        self::Inject($container, $instance);
    }

    public static function Inject(ContainerInterface $container, $instance) {
        if ($instance instanceof LazyLoadingInterface && !$instance->isProxyInitialized()) {
            return;
        }

        if ($instance instanceof EntityFilterManagerAware) {
            $entityFilterManager = $container->get(EntityFilterManager::class);
            $instance->setEntityFilterManager($entityFilterManager);
        }
    }
}
