<?php

namespace EcampCore\Printable;

use EcampCore\Repository\PeriodRepository;
use EcampLib\Printable\PrintableInterface;
use Zend\View\Model\ViewModel;

class Picasso implements PrintableInterface
{
    /** @var PeriodRepository */
    private $periodRepository;

    public function __construct(
        PeriodRepository $periodRepository
    ) {
        $this->periodRepository = $periodRepository;
    }

    public function create(array $item)
    {
        $periodId = $item['periodId'];
        $start = $item['start'] ?: 420;
        $end = $item['end'] ?: 1440;

        $period = $this->periodRepository->find($periodId);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-core/printable/picasso.twig');
        $viewModel->setVariable('period', $period);
        $viewModel->setVariable('dayStart', $start);
        $viewModel->setVariable('dayEnd', $end);

        return $viewModel;
    }
}
