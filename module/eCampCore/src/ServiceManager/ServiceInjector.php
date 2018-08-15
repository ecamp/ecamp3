<?php

namespace eCamp\Core\ServiceManager;

use eCamp\Lib\ServiceManager\AclInjector;
use eCamp\Lib\ServiceManager\EntityFilterManagerInjector;
use eCamp\Lib\ServiceManager\EntityManagerInjector;
use eCamp\Lib\ServiceManager\HydratorPluginManagerInjector;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

final class ServiceInjector implements InitializerInterface {
    public function __invoke(ContainerInterface $container, $instance) {
        self::Inject($container, $instance);
    }

    public static function Inject(ContainerInterface $container, $instance) {
        EntityServiceInjector::Inject($container, $instance);

        AuthUserProviderInjector::Inject($container, $instance);

        AclInjector::Inject($container, $instance);
        EntityManagerInjector::Inject($container, $instance);
        EntityFilterManagerInjector::Inject($container, $instance);
        HydratorPluginManagerInjector::Inject($container, $instance);
    }
}