<?php

namespace EcampCore\Printable;

use EcampCore\Repository\CampRepository;
use EcampLib\Printable\PrintableInterface;
use Zend\View\Model\ViewModel;

class Cover implements PrintableInterface
{
    /** @var CampRepository */
    private $campRepository;

    public function __construct(
        CampRepository $campRepository
    ) {
        $this->campRepository = $campRepository;
    }

    public function create(array $item)
    {
        $campId = $item['campId'];

        $camp = $this->campRepository->find($campId);

        if ($camp != null) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-core/printable/cover.twig');
            $viewModel->setVariable('camp', $camp);

            return $viewModel;
        }

        return null;
    }
}
