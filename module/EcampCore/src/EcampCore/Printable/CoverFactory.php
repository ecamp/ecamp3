<?php

namespace EcampCore\Printable;

use EcampLib\ServiceManager\PrintableManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CoverFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var PrintableManager $printableManager */
        $printableManager = $serviceLocator;
        $serviceLocator = $printableManager->getServiceLocator();

        /** @var \EcampCore\Repository\CampRepository $campRepository */
        $campRepository = $serviceLocator->get('EcampCore\Repository\Camp');

        return new Cover($campRepository);
    }
}
