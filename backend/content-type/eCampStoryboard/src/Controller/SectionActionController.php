<?php

namespace eCamp\ContentType\Storyboard\Controller;

use eCamp\ContentType\Storyboard\Service\SectionService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class SectionActionController extends AbstractActionController {
    private SectionService $sectionService;

    public function __construct(SectionService $sectionService) {
        $this->sectionService = $sectionService;
    }

    public function moveUpAction(): ViewModel {
        $sectionId = $this->params()->fromRoute('sectionId');

        $this->sectionService->moveUp($sectionId);

        return $this->forward()->dispatch(
            SectionController::class,
            ['action' => null, 'sectionId' => $sectionId]
        );
    }

    public function moveDownAction(): ViewModel {
        $sectionId = $this->params()->fromRoute('sectionId');

        $this->sectionService->moveDown($sectionId);

        return $this->forward()->dispatch(
            SectionController::class,
            ['action' => null, 'sectionId' => $sectionId]
        );
    }
}
