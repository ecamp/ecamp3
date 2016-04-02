<?php

namespace EcampLib\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Provides wrapped services for all service classes
 * Pattern: Pattern: Ecamp*\Service\*
 */
class AbstractServiceFactory implements AbstractFactoryInterface
{
    const Factory = 'factory';
    const ServiceInstance = 'serviceinstance';

    private $cache = array();

    /** @var \EcampLib\Options\ModuleOptions */
    private $moduleOptions = null;

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (array_key_exists($requestedName, $this->cache)) {
            return true;
        }

        if ($this->moduleOptions == null) {
            $this->moduleOptions = $serviceLocator->get('EcampLib\Options\ModuleOptions');
        }

        $canCreateService = false;

        foreach ($this->moduleOptions->getServiceMappings() as $servicePattern => $serviceMapping) {
            if (preg_match($servicePattern, $requestedName)) {
                $serviceFactory = preg_replace($servicePattern, $serviceMapping[self::Factory], $requestedName);
                $canCreateService = true;

                $this->cache[$requestedName] = array(
                    self::Factory => $serviceFactory
                );

                break;
            }
        }

        return $canCreateService;
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (!$this->canCreateServiceWithName($serviceLocator, $name, $requestedName)) {
            return null;
        }

        $cacheEntry = $this->cache[$requestedName];

        if ($cacheEntry[self::ServiceInstance] == null) {
            $cacheEntry[self::ServiceInstance] = new ServiceWrapper($cacheEntry[self::Factory]);
        }

        return $cacheEntry[self::ServiceInstance];
    }
}
