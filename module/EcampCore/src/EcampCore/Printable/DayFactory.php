<?php

namespace EcampCore\Printable;

use EcampLib\ServiceManager\PrintableManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DayFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var PrintableManager $printableManager */
        $printableManager = $serviceLocator;
        $serviceLocator = $printableManager->getServiceLocator();

        /** @var \EcampCore\Repository\DayRepository $dayRepository */
        $dayRepository = $serviceLocator->get('EcampCore\Repository\Day');

        return new Day($dayRepository);
    }
}
