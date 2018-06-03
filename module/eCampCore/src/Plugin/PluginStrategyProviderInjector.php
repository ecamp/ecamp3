<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\EntityServiceAware\PluginStrategyProviderAware;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class PluginStrategyProviderInjector implements InitializerInterface
{

    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof PluginStrategyProviderAware) {
            $instance->setPluginStrategyProvider($container->get(PluginStrategyProvider::class));
        }
    }
}
