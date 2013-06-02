<?php

namespace EcampCore\Acl;

use EcampLib\Acl\Acl;
use EcampLib\Acl\RoleFactoryInterface;

use EcampCore\Entity\User;

class UserRoleFactory
	implements RoleFactoryInterface
{
	/**
	 * @var \EcampLib\Acl\Acl
	 */
	private $acl;
	
	public function __construct(Acl $acl){
		$this->acl = $acl;
	}
	
	public function createRole($user){
		if(! $user instanceof User){
			throw new \InvalidArgumentException("EcampCore\Entity\User required!");
		}
		
		$userRole = new Role\UserRole($user);
		
		if(! $this->acl->hasRole($userRole)){
			$this->acl->addRole($userRole, $user->getRole());
		}
		
		return $userRole;
	}
	
}
