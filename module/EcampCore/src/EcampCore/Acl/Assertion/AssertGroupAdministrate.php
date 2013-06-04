<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Acl\Role\UserRole;
use EcampCore\Acl\Resource\GroupResource;

class AssertGroupAdministrate
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ){
        if ($role instanceof UserRole && $resource instanceof GroupResource) {
            $user = $role->getUser();
            $group = $resource->getGroup();

            if ($group->isManager($user)) {
                return true;
            }

            return false;
        }
    }
}
