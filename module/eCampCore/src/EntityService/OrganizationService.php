<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\OrganizationHydrator;
use eCamp\Core\Entity\Organization;

class OrganizationService extends AbstractEntityService {
    public function __construct() {
        parent::__construct(
            Organization::class,
            OrganizationHydrator::class
        );
    }
}
