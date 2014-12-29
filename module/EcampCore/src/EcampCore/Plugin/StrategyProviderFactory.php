<?php

namespace EcampCore\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StrategyProviderFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        return new StrategyProvider($services);
    }
}
