<?php

namespace EcampLib\Acl;

class RoleFactory
    implements RoleFactoryInterface
{
    public function createRole($role)
    {
        return $role;
    }
}
