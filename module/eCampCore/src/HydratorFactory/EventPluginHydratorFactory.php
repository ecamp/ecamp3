<?php

namespace eCamp\Core\HydratorFactory;

use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Plugin\PluginStrategyProvider;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class EventPluginHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventPluginHydrator
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var PluginStrategyProvider $pluginStrategyProvider */
        $pluginStrategyProvider = $container->get(PluginStrategyProvider::class);

        return new EventPluginHydrator($pluginStrategyProvider);
    }
}
