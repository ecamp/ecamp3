<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface MediumServiceAware {
    /**
     * @return EntityService\MediumService
     */
    public function getMediumService();

    /**
     * @param EntityService\MediumService $mediumService
     */
    public function setMediumService(EntityService\MediumService $mediumService);
}
