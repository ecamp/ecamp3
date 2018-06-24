<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait UserIdentityServiceTrait {
    /** @var EntityService\UserIdentityService */
    private $userIdentityService;

    public function setUserIdentityService(EntityService\UserIdentityService $userIdentityService) {
        $this->userIdentityService = $userIdentityService;
    }

    public function getUserIdentityService() {
        return $this->userIdentityService;
    }
}
