<?php

namespace EcampCore\Acl\Assertion;

use EcampCore\Entity\Camp;
use EcampCore\Entity\User;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;

class ContributorAssertion extends AssertionInterface
{
	
	public function assert(
		Acl $acl, 
		RoleInterface $role = null, 
		ResourceInterface $resource = null, 
		$privilege = null
	){
		if(! $role instanceof User){
			// Only a User can be a Contributor
			return false;
		} else {
			$user = $role;
		}
		
		if(! $resource instanceof Camp){
			// A User can only be a Contributor of a Camp
			return false;
		} else {
			$camp = $resource;
		}
		
		if(! $user->getAcceptedUserCamps()->contains($camp)){
			// User is not a Contributor
			return false;
		}
		
		
		
	}
	
}
