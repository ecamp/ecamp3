<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Entity\Camp;
use EcampCore\Entity\User;

class AssertCampList
    implements AssertionInterface
{
    public function assert(
        Acl $acl,
        RoleInterface $user = null,
        ResourceInterface $camp = null,
        $privilege = null
    ){
        if ($user instanceof User && $camp instanceof Camp) {

            // If Camp is public
            if ($camp->getVisibility() == Camp::VISIBILITY_PUBLIC) {
                return true;
            }

            // If User is Member
            if($camp->campCollaboration()->isMember($user))		return true;

            // If User is Guest
            if($camp->campCollaboration()->isGuest($user))		return true;

            // If User is Manager
            if($camp->campCollaboration()->isManager($user))		return true;

            // If User is Owner
            if($camp->getOwner() == $user)	return true;

            // If Camp belongs to Group and User can administrate that group
            if (null != ($group = $camp->getOwner())) {
                return $acl->isAllowed($user, $group, 'administrate');
            }
        }

        return false;
    }
}
