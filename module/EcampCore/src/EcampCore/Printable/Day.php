<?php

namespace EcampCore\Printable;

use EcampCore\Repository\DayRepository;
use EcampLib\Printable\PrintableInterface;
use Zend\View\Model\ViewModel;

class Day implements PrintableInterface
{
    /** @var DayRepository */
    private $dayRepository;

    public function __construct(
        DayRepository $dayRepository
    ) {
        $this->dayRepository = $dayRepository;
    }

    public function create(array $item)
    {
        $dayId = $item['dayId'];

        $day = $this->dayRepository->find($dayId);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-core/printable/day.twig');
        $viewModel->setVariable('day', $day);

        return $viewModel;
    }
}
