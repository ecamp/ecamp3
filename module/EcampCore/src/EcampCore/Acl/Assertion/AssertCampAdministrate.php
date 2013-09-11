<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Entity\User;
use EcampCore\Entity\Camp;

class AssertCampAdministrate
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $user = null,
        ResourceInterface $camp = null,
        $privilege = null
    ){
        if ($user instanceof User && $camp instanceof Camp) {

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
