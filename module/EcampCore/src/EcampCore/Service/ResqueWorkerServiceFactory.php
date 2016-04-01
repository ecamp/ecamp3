<?php

namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResqueWorkerServiceFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        return new ResqueWorkerService();
    }
}
