<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Acl\Role\UserRole;
use EcampCore\Auth\AuthenticationService;

class AssertAdminInSupportModus
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ){
        if ($role instanceof UserRole) {

            $auth = new AuthenticationService();
            $storage = $auth->getOrigStorage();

            return !$storage->isEmpty();
        }

        return false;
    }
}
