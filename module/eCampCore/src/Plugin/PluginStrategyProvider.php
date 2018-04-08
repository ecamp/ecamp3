<?php

namespace eCamp\Core\Plugin;

use eCamp\Core\Entity\Plugin;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PluginStrategyProvider
{
    /** @var  ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @param $pluginOrStrategyClass
     * @return PluginStrategyInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get($pluginOrStrategyClass)
    {
        $strategyClass = $pluginOrStrategyClass;

        if ($pluginOrStrategyClass instanceof Plugin) {
            $strategyClass = $pluginOrStrategyClass->getStrategyClass();
        }

        $strategy = null;
        if (is_string($strategyClass)) {
            if ($this->container->has($strategyClass)) {
                $strategy = $this->container->get($strategyClass);
            } elseif (class_exists($strategyClass)) {
                $strategy = new $strategyClass();
            }
        }

        return $strategy;
    }
}
