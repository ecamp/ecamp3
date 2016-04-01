<?php

namespace EcampCore\Service\Factory;

use EcampCore\Service\ResqueWorkerService;
use Zend\ServiceManager\Exception;
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
