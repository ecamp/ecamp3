<?php

namespace EcampLib\ServiceManager;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class PrintableManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'EcampLib\ServiceManager\PrintableManager';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return parent::createService($serviceLocator);
    }
}
