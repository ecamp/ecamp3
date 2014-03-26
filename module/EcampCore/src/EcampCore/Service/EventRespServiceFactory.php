<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventRespServiceFactory
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
        $eventRespRepository = $services->get('EcampCore\Repository\EventResp');

        return new EventRespService($eventRespRepository);
    }
}
