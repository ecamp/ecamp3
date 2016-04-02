<?php

namespace EcampCore\Service\Factory;

use EcampCore\Service\ResqueJobService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResqueJobServiceFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var \EcampLib\ServiceManager\JobFactoryManager $jobFactoryManager */
        $jobFactoryManager = $services->get('JobFactoryManager');
        $jobQueue = $services->get('EcampLib\Job\JobQueue');

        return new ResqueJobService($jobFactoryManager, $jobQueue);
    }
}
