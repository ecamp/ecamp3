<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface DayServiceAware {
    /**
     * @return EntityService\DayService
     */
    public function getDayService();

    /**
     * @param EntityService\DayService $dayService
     */
    public function setDayService(EntityService\DayService $dayService);
}
