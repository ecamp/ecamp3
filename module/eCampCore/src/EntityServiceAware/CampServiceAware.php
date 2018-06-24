<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface CampServiceAware {
    /**
     * @return EntityService\CampService
     */
    public function getCampService();

    /**
     * @param EntityService\CampService $campService
     */
    public function setCampService(EntityService\CampService $campService);
}
