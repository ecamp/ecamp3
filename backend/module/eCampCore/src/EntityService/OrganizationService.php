<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\OrganizationHydrator;
use eCamp\Core\Entity\Organization;
use eCamp\Lib\Service\ServiceUtils;

class OrganizationService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            Organization::class,
            OrganizationHydrator::class
        );
    }
}
