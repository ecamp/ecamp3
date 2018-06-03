<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait PeriodServiceTrait
{
    /** @var EntityService\PeriodService */
    private $periodService;

    public function setPeriodService(EntityService\PeriodService $periodService) {
        $this->periodService = $periodService;
    }

    public function getPeriodService() {
        return $this->periodService;
    }

}
