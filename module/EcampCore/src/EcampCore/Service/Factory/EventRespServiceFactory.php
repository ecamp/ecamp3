<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\EventRespService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventRespServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return EventRespService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $eventRespRepository = $services->get('EcampCore\Repository\EventResp');

        return new EventRespService($eventRespRepository);
    }
}
