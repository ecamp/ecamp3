<?php

namespace EcampCore\Acl\Assertion;

use EcampCore\Entity\Camp;

use EcampCore\Acl\Role;
use EcampCore\Acl\Resource;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;

class CampOwner extends AssertionInterface
{
	
	public function assert(
		Acl $acl, 
		RoleInterface $role = null, 
		ResourceInterface $resource = null, 
		$privilege = null
	){
		if($role instanceof Role){
			$user = $role->getUser();
		} else {
			// Only a User can be a CampOwner
			return false;
		}

		if($resource instanceof Resource && $resource->getEntity() instanceof Camp){
			$camp = $resource->getEntity();
		} else {
			// Only a Camp can have a CampOwner
			return false;
		}
		
		return $camp->getOwner()->getId() == $user->getId();
	}
}
