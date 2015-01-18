<?php

namespace EcampCore\Plugin;

use EcampCore\Entity\Plugin;
use Zend\ServiceManager\ServiceLocatorInterface;

class StrategyProvider
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    protected $pluginStrategies = array();

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @param  Plugin           $plugin
     * @return AbstractStrategy
     */
    public function Get(Plugin $plugin)
    {
        $pluginStrategyClass = $plugin->getStrategyClass();

        if (!array_key_exists($pluginStrategyClass, $this->pluginStrategies)) {
            /** @var \EcampCore\Plugin\AbstractStrategyFactory $pluginStrategyFactory */
            $pluginStrategyFactory = $this->serviceLocator->get($pluginStrategyClass);

            $pluginStrategy = $pluginStrategyFactory->createStrategy();
            $this->pluginStrategies[$pluginStrategyClass] = $pluginStrategy;

            return $pluginStrategy;
        }

        return $this->pluginStrategies[$pluginStrategyClass];
    }

}
