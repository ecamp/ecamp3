<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventServiceFactory
    implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $services
     * @return CampService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return new EventService(
            $services->get('EcampCore\Plugin\StrategyProvider'),
            $services->get('EcampCore\Repository\Event'),
            $services->get('EcampCore\Repository\EventCategory')
        );
    }
}
