<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait DayServiceTrait {
    /** @var EntityService\DayService */
    private $dayService;

    public function setDayService(EntityService\DayService $dayService) {
        $this->dayService = $dayService;
    }

    public function getDayService() {
        return $this->dayService;
    }
}
