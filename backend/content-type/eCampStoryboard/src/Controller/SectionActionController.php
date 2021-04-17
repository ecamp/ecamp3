<?php

namespace eCamp\ContentType\Storyboard\Controller;

use eCamp\ContentType\Storyboard\Service\SectionService;
use Laminas\Mvc\Controller\AbstractActionController;

class SectionActionController extends AbstractActionController {
    private SectionService $sectionService;

    public function __construct(SectionService $sectionService) {
        $this->sectionService = $sectionService;
    }
}
