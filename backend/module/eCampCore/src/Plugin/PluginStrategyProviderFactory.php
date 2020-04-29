<?php

namespace eCamp\Core\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PluginStrategyProviderFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @return PluginStrategyProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new PluginStrategyProvider($container);
    }
}
