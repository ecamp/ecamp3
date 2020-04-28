<?php

namespace eCamp\Core\HydratorFactory;

use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Plugin\PluginStrategyProvider;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class EventPluginHydratorFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return EventPluginHydrator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var PluginStrategyProvider $pluginStrategyProvider */
        $pluginStrategyProvider = $container->get(PluginStrategyProvider::class);

        return new EventPluginHydrator($pluginStrategyProvider);
    }
}
