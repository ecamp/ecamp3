<?php

namespace eCamp\Plugin\Storyboard\Controller;

use eCamp\Plugin\Storyboard\Service\SectionService;
use Zend\Mvc\Controller\AbstractActionController;

class SectionActionController extends AbstractActionController {
    /** @var SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService) {
        $this->sectionService = $sectionService;
    }


    public function moveUpAction() {
        $sectionId = $this->params()->fromRoute('section_id');

        $this->sectionService->moveUp($sectionId);

        return $this->forward()->dispatch(
            SectionController::class,
            ['action' => null, 'section_id' => $sectionId]
        );
    }

    public function moveDownAction() {
        $sectionId = $this->params()->fromRoute('section_id');

        $this->sectionService->moveDown($sectionId);

        return $this->forward()->dispatch(
            SectionController::class,
            ['action' => null, 'section_id' => $sectionId]
        );
    }
}
