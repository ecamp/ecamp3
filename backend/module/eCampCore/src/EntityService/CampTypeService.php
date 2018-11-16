<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\CampTypeHydrator;
use eCamp\Core\Entity\CampType;
use eCamp\Lib\Service\ServiceUtils;

class CampTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            CampType::class,
            CampTypeHydrator::class
        );
    }
}
