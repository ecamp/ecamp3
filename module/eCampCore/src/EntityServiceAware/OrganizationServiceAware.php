<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface OrganizationServiceAware {
    /**
     * @return EntityService\OrganizationService
     */
    public function getOrganizationService();

    /**
     * @param EntityService\OrganizationService $organizationService
     */
    public function setOrganizationService(EntityService\OrganizationService $organizationService);
}
