<?php

namespace EcampCore\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use EcampCore\Acl\Rolr\UserRole;
use EcampCore\Acl\Resource\UserResource;

class AssertUserAdministrate
	implements AssertionInterface
{
	public function assert(
		Acl $acl, 
		RoleInterface $role = null, 
		ResourceInterface $resource = null, 
		$privilege = null
	){
		if($role instanceof UserRole && $resource instanceof UserResource){
			$me = $role->getUser();
			$user = $resource->getUser();
			
			if($me == $user)	return true;
			
			return false;
		}
	}
}