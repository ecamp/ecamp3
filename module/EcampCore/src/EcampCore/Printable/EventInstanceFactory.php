<?php

namespace EcampCore\Printable;

use EcampLib\ServiceManager\PrintableManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventInstanceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var PrintableManager $printableManager */
        $printableManager = $serviceLocator;
        $serviceLocator = $printableManager->getServiceLocator();

        /** @var \EcampCore\Plugin\StrategyProvider $strategyProvider */
        $strategyProvider = $serviceLocator->get('EcampCore\Plugin\StrategyProvider');

        /** @var \EcampCore\Repository\MediumRepository $mediumRepository */
        $mediumRepository = $serviceLocator->get('EcampCore\Repository\Medium');

        /** @var \EcampCore\Repository\EventTemplateRepository $eventTemplateRepository */
        $eventTemplateRepository = $serviceLocator->get('EcampCore\Repository\EventTemplate');

        /** @var \EcampCore\Repository\EventInstanceRepository $eventInstanceRepository  */
        $eventInstanceRepository = $serviceLocator->get('EcampCore\Repository\EventInstance');

        return new EventInstance(
            $strategyProvider,
            $mediumRepository,
            $eventTemplateRepository,
            $eventInstanceRepository
        );
    }
}
