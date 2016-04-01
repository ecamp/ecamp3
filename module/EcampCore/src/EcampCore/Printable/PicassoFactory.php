<?php

namespace EcampCore\Printable;

use EcampLib\ServiceManager\PrintableManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PicassoFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var PrintableManager $printableManager */
        $printableManager = $serviceLocator;
        $serviceLocator = $printableManager->getServiceLocator();

        /** @var \EcampCore\Repository\PeriodRepository $periodRepository */
        $periodRepository = $serviceLocator->get('EcampCore\Repository\Period');

        return new Picasso($periodRepository);
    }

}
