<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\PeriodService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PeriodServiceFactory
    implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $services
     * @return PeriodService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $dayService = $services->get('EcampCore\Service\Day');

        return new PeriodService($dayService);
    }
}
