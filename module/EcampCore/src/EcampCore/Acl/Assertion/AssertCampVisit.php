<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Entity\User;
use EcampCore\Entity\Camp;

class AssertCampVisit
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $user = null,
        ResourceInterface $camp = null,
        $privilege = null
    ){
        if ($user instanceof User && $camp instanceof Camp) {

            // If User is Member
            if($camp->campCollaboration()->isMember($user))		return true;

            // If User is Guest
            if($camp->campCollaboration()->isGuest($user))		return true;

            // If User is Manager
            if($camp->campCollaboration()->isManager($user))		return true;

            // If User is Owner
            if($camp->getOwner() == $user)	return true;

            // If Camp belongs to Group and User can administrate that group
            if (null != ($group = $camp->getGroup())) {
                return $acl->isAllowed($user, $group, 'administrate');
            }
        }

        return false;
    }
}
