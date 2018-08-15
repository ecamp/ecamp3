<?php

namespace eCamp\Core\ServiceManager;

use eCamp\Core\EntityService;
use eCamp\Core\EntityServiceAware;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

final class EntityServiceInjector implements InitializerInterface
{
    public function __invoke(ContainerInterface $container, $instance) {
        self::Inject($container, $instance);
    }

    public static function Inject(ContainerInterface $container, $instance) {
[InjectBody]
    }
}
