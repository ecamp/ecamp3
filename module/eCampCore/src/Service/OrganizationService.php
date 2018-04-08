<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\OrganizationHydrator;
use eCamp\Core\Entity\Organization;
use eCamp\Lib\Service\BaseService;

class OrganizationService extends BaseService
{
    public function __construct(OrganizationHydrator $organizationHydrator) {
        parent::__construct($organizationHydrator, Organization::class);
    }
}
