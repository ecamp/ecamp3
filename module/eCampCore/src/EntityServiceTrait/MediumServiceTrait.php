<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait MediumServiceTrait {
    /** @var EntityService\MediumService */
    private $mediumService;

    public function setMediumService(EntityService\MediumService $mediumService) {
        $this->mediumService = $mediumService;
    }

    public function getMediumService() {
        return $this->mediumService;
    }
}
