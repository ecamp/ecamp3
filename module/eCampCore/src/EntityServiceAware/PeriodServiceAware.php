<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface PeriodServiceAware {
    /**
     * @return EntityService\PeriodService
     */
    public function getPeriodService();

    /**
     * @param EntityService\PeriodService $periodService
     */
    public function setPeriodService(EntityService\PeriodService $periodService);
}
