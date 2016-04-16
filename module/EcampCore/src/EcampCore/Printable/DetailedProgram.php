<?php

namespace EcampCore\Printable;

use EcampCore\Repository\DayRepository;
use EcampCore\Repository\EventInstanceRepository;
use EcampCore\Repository\PeriodRepository;
use EcampLib\Printable\PrintableInterface;
use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class DetailedProgram implements PrintableInterface
{
    /** @var View */
    private $view;

    /** @var PeriodRepository */
    private $periodRepository;

    /** @var DayRepository */
    private $dayRepository;

    /** @var EventInstanceRepository */
    private $eventInstanceRepository;

    /** @var Day */
    private $dayPrintable;

    /** @var EventInstance */
    private $eventInstancePrintable;

    public function __construct(
        View $view,
        PeriodRepository $periodRepository,
        DayRepository $dayRepository,
        EventInstanceRepository $eventInstanceRepository,
        Day $dayPrintable,
        EventInstance $eventInstancePrintable
    ) {
        $this->view = $view;
        $this->periodRepository = $periodRepository;
        $this->dayRepository = $dayRepository;
        $this->eventInstanceRepository = $eventInstanceRepository;
        $this->dayPrintable = $dayPrintable;
        $this->eventInstancePrintable = $eventInstancePrintable;
    }

    /**
     * @param  ModelInterface $viewModel
     * @return string
     */
    private function render(ModelInterface $viewModel)
    {
        $viewModel->setOption('has_parent', true);

        return $this->view->render($viewModel);
    }

    public function create(array $item)
    {
        $periodId = $item['periodId'];

        $days = "";
        $dayEntities = $this->dayRepository->findPeriodDays($periodId);

        /** @var \EcampCore\Entity\Day $day */
        foreach ($dayEntities as $day) {
            $dayViewModel = new ViewModel();
            $dayViewModel->setTemplate('ecamp-core/printable/detailed-program-day.twig');
            $dayViewModel->setVariable('day', $this->render(
                $this->dayPrintable->create(array('dayId' => $day->getId()))
            ));

            $eventInstances = "";
            /** @var \EcampCore\Entity\EventInstance $eventInstance */
            foreach ($day->getEventInstances() as $eventInstance) {
                $eventInstances .= $this->render(
                    $this->eventInstancePrintable->create(
                        array('eventInstanceId' => $eventInstance->getId())
                    )
                );
            }
            $dayViewModel->setVariable('eventInstances', $eventInstances);

            $days .= $this->render($dayViewModel);
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-core/printable/detailed-program.twig');
        $viewModel->setVariable('days', $days);

        return $viewModel;
    }
}
