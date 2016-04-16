<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\EventPluginService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventPluginServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return EventPluginService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $eventPluginRepository = $services->get('EcampCore\Repository\EventPlugin');

        return new EventPluginService($eventPluginRepository);
    }
}
