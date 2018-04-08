<?php

namespace eCamp\Lib\ServiceManager;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class EntityFilterManagerInjector implements InitializerInterface
{
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof EntityFilterManagerAware) {
            $entityFilterManager = $container->get(EntityFilterManager::class);
            $instance->setEntityFilterManager($entityFilterManager);
        }
    }
}
