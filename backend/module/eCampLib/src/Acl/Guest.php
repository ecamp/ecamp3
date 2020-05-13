<?php

namespace eCamp\Lib\Acl;

use Laminas\Permissions\Acl\Role\RoleInterface;

class Guest implements RoleInterface {
    public function getRoleId() {
        return __CLASS__;
    }
}
