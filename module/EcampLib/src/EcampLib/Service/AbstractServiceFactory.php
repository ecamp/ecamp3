<?php

namespace EcampLib\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

/**
 * Provides wrapped services for all internal service classes
 * Pattern: Pattern: Ecamp*\Service\*
 */
class AbstractServiceFactory implements AbstractFactoryInterface
{
    private $orm;

    private $pattern = "/^Ecamp(\w+)\\\\Service\\\\(\w+)$/";

    public function __construct($orm = null)
    {
        $this->orm = $orm ?: 'doctrine.entitymanager.orm_default';
    }

    private function getInternalServiceName($serviceName)
    {
        return $serviceName . '\\Internal';
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return preg_match($this->pattern,$requestedName) && $serviceLocator->has($this->getInternalServiceName($requestedName));
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        /* load internal service and wrap it with ServiceWrapper for external use */
        $service = $serviceLocator->get($this->getInternalServiceName($requestedName));
        $em = $serviceLocator->get($this->orm);

        return new ServiceWrapper($em, $service);
    }
}
