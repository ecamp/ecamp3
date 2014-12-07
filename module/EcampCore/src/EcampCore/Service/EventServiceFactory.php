<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventServiceFactory
    implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface              $services
     * @return CampService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $eventCategoryRepo = $services->get('EcampCore\Repository\EventCategory');

        return new EventService($eventCategoryRepo);
    }
}
