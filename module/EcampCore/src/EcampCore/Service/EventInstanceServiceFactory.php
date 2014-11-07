<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventInstanceServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return CampService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $eventInstanceRepo = $services->get('EcampCore\Repository\EventInstance');
        $dayRepo = $services->get('EcampCore\Repository\Day');

        return new EventInstanceService($eventInstanceRepo, $dayRepo);
    }
}
