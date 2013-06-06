<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;

class AssertGroupAdministrate
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $user = null,
        ResourceInterface $group = null,
        $privilege = null
    ){
        if ($user instanceof User && $group instanceof Group) {

            if ($group->isManager($user)) {
                return true;
            }
        }

        return false;
    }
}
