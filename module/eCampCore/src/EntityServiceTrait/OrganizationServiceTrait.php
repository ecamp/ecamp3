<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait OrganizationServiceTrait
{
    /** @var EntityService\OrganizationService */
    private $organizationService;

    public function setOrganizationService(EntityService\OrganizationService $organizationService) {
        $this->organizationService = $organizationService;
    }

    public function getOrganizationService() {
        return $this->organizationService;
    }

}
