<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Auth\AuthenticationService;
use EcampCore\Entity\User;

class AssertAdminInSupportModus
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $user = null,
        ResourceInterface $resource = null,
        $privilege = null
    ){
        if ($user instanceof User) {

            $auth = new AuthenticationService();

            return $auth->hasOrigIdentity();
        }

        return false;
    }
}
