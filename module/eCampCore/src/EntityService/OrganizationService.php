<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\OrganizationHydrator;
use eCamp\Core\Entity\Organization;
use eCamp\Lib\Service\ServiceUtils;

class OrganizationService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            Organization::class,
            OrganizationHydrator::class
        );
    }
}
