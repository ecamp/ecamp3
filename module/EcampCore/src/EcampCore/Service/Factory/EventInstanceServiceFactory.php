<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\EventInstanceService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventInstanceServiceFactory
    implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $services
     * @return EventInstanceService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return new EventInstanceService(
            $services->get('EcampCore\Repository\Period'),
            $services->get('EcampCore\Repository\Day'),
            $services->get('EcampCore\Repository\EventInstance')
        );
    }
}
