<?php

namespace EcampCore\Acl\Assertion;

use EcampCore\Entity\Group;

use EcampCore\Acl\Role;
use EcampCore\Acl\Resource;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;

class GroupMember extends AssertionInterface
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
			return false;
		}

		if($resource instanceof Resource && $resource->getEntity() instanceof Group){
			$group = $resource->getEntity();
		} else {
			return false;
		}
		
		return ($group->isMember($user) || $group->isManager($user));
	}
}
