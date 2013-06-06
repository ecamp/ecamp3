<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Entity\User;

class AssertUserAdministrate
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $me = null,
        ResourceInterface $user = null,
        $privilege = null
    ){
        if ($me instanceof User && $user instanceof User) {
            return ($me == $user);

        }

        return false;
    }
}
