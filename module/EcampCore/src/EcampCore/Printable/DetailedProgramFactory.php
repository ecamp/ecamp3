<?php

namespace EcampCore\Printable;

use EcampCore\Repository\EventInstanceRepository;
use EcampCore\Repository\PeriodRepository;
use EcampLib\ServiceManager\PrintableManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DetailedProgramFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var PrintableManager $printableManager */
        $printableManager = $serviceLocator;
        $serviceLocator = $printableManager->getServiceLocator();

        /** @var \Zend\View\View $view */
        $view = $serviceLocator->get('View');

        /** @var PeriodRepository $periodRepository */
        $periodRepository = $serviceLocator->get('EcampCore\Repository\Period');

        /** @var \EcampCore\Repository\DayRepository $dayRepository */
        $dayRepository = $serviceLocator->get('EcampCore\Repository\Day');

        /** @var EventInstanceRepository $eventInstanceRepository */
        $eventInstanceRepository = $serviceLocator->get('EcampCore\Repository\EventInstance');

        /** @var Day $dayPrintable */
        $dayPrintable = $printableManager->get('Day');

        /** @var EventInstance $eventInstancePrintable */
        $eventInstancePrintable = $printableManager->get('EventInstance');

        return new DetailedProgram(
            $view,
            $periodRepository,
            $dayRepository,
            $eventInstanceRepository,
            $dayPrintable,
            $eventInstancePrintable
        );
    }

}