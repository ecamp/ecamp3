<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait UserServiceTrait {
    /** @var EntityService\UserService */
    private $userService;

    public function setUserService(EntityService\UserService $userService) {
        $this->userService = $userService;
    }

    public function getUserService() {
        return $this->userService;
    }
}
