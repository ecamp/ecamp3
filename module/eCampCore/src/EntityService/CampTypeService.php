<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\CampTypeHydrator;
use eCamp\Core\Entity\CampType;

class CampTypeService extends AbstractEntityService {
    public function __construct() {
        parent::__construct(
            CampType::class,
            CampTypeHydrator::class
        );
    }
}
