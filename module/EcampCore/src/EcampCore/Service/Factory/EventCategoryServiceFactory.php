<?php

namespace EcampCore\Service\Factory;

use EcampCore\Service\EventCategoryService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventCategoryServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $eventTypeRepo = $services->get('EcampCore\Repository\EventType');

        return new EventCategoryService($eventTypeRepo);
    }
}
