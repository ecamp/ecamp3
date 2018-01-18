<?php

namespace eCamp\Lib\Acl;

use Zend\Permissions\Acl\Role\RoleInterface;

class Guest implements RoleInterface
{
    public function getRoleId() {
        return __CLASS__;
    }
}
