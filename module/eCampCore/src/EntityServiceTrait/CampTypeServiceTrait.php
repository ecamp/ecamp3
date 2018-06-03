<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait CampTypeServiceTrait
{
    /** @var EntityService\CampTypeService */
    private $campTypeService;

    public function setCampTypeService(EntityService\CampTypeService $campTypeService) {
        $this->campTypeService = $campTypeService;
    }

    public function getCampTypeService() {
        return $this->campTypeService;
    }

}
