<?php

namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResqueJobServiceFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \EcampLib\ServiceManager\JobFactoryManager $jobFactoryManager */
        $jobFactoryManager = $services->get('JobFactoryManager');

        return new ResqueJobService($jobFactoryManager);
    }
}
