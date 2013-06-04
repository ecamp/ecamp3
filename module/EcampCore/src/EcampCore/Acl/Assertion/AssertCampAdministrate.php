<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Acl\Role\UserRole;
use EcampCore\Acl\Resource\CampResource;

class AssertCampAdministrate
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ){
        if ($role instanceof UserRole && $resource instanceof CampResource) {
            $user = $role->getUser();
            $camp = $resource->getCamp();

            // If User is Owner
            if($camp->getOwner() == $user)	return true;

            // If Camp belongs to Group and User can administrate that group
            if (null != ($group = $camp->getGroup())) {
                return $acl->isAllowed($user, $group, $privilege);
            }
        }

        return false;
    }
}
