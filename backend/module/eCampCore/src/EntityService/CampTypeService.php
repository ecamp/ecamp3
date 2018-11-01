<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\CampTypeHydrator;
use eCamp\Core\Entity\CampType;
use eCamp\Lib\Service\ServiceUtils;

class CampTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            CampType::class,
            CampTypeHydrator::class
        );
    }
}
