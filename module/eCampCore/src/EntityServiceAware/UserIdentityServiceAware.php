<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface UserIdentityServiceAware
{
    /**
     * @return EntityService\UserIdentityService
     */
    public function getUserIdentityService();

    /**
     * @param EntityService\UserIdentityService $userIdentityService
     */
    public function setUserIdentityService(EntityService\UserIdentityService $userIdentityService);
}
