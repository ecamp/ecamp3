<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface CampTypeServiceAware {
    /**
     * @return EntityService\CampTypeService
     */
    public function getCampTypeService();

    /**
     * @param EntityService\CampTypeService $campTypeService
     */
    public function setCampTypeService(EntityService\CampTypeService $campTypeService);
}
