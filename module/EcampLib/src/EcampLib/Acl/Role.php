<?php

namespace EcampLib\Acl;

use EcampCore\Entity\User;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;

class Role implements RoleInterface
{
	private $user;
	
	public function __construct(User $user){
		$this->user = $user;
	}
	
	/**
	 * @return User
	 */
	public function getUser(){
		return $this->user;
	}
	
	public function getRoleId(){
		return "EcampCore\Entity\User::" . $this->user->getId();
	}
	
	public function register(Acl $acl){
		if(! $acl->hasRole($this)){
			$acl->addRole($this, $this->user->getRole());
		}
	}
	
}