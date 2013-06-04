<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CampServiceFactory
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
        $periodService  = $services->get('EcampCore\Service\Period\Internal');
        $campRepo 		= $services->get('EcampCore\Repository\Camp');

        $campService = new CampService($campRepo, $periodService);

        return $campService;
    }
}
