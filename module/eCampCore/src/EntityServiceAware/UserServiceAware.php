<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface UserServiceAware
{
    /**
     * @return EntityService\UserService
     */
    public function getUserService();

    /**
     * @param EntityService\UserService $userService
     */
    public function setUserService(EntityService\UserService $userService);
}
