<?php

namespace EcampCore\Acl\Resource;

use EcampCore\Entity\User;
use Zend\Permissions\Acl\Resource\ResourceInterface;

class UserResource
	implements ResourceInterface
{
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @param User $user
	 */
	public function __construct(User $user){
		$this->user = $user;
	}
	
	/**
	 * @return User
	 */
	public function getUser(){
		return $this->user;
	}
	
	
	public function getResourceId(){
		return 'EcampCore\Entity\User::' . $this->user->getId();
	}
	
}