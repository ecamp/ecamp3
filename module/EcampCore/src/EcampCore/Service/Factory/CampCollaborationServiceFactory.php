<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\CampCollaborationService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CampCollaborationServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return CampCollaborationService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $campCollaborationRepo 	= $services->get('EcampCore\Repository\CampCollaboration');

        $campCollabtorationService = new CampCollaborationService($campCollaborationRepo);

        return $campCollabtorationService;
    }
}
