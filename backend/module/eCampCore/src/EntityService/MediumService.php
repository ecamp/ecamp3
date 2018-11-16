<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\MediumHydrator;
use eCamp\Core\Entity\Medium;
use eCamp\Lib\Service\ServiceUtils;

class MediumService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            Medium::class,
            MediumHydrator::class
        );
    }
}
