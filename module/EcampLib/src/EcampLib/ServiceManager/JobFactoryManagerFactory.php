<?php

namespace EcampLib\ServiceManager;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobFactoryManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'EcampLib\ServiceManager\JobFactoryManager';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return parent::createService($serviceLocator);
    }
}
