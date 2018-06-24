<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait CampServiceTrait {
    /** @var EntityService\CampService */
    private $campService;

    public function setCampService(EntityService\CampService $campService) {
        $this->campService = $campService;
    }

    public function getCampService() {
        return $this->campService;
    }
}
