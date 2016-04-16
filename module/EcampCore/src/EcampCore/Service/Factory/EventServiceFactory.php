<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\EventService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventServiceFactory
    implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $services
     * @return EventService
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
