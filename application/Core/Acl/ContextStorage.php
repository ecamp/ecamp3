<?php

namespace Core\Acl;


use CoreApi\Acl\Context;
use CoreApi\Entity\User;

class ContextStorage
{
	
	
	/**
	 * @var Core\Repository\UserRepository
	 * @Inject Core\Repository\UserRepository 
	 */
	private $userRepo;
	
	/**
	 * @var Core\Repository\GroupRepository
	 * @Inject Core\Repository\GroupRepository
	 */
	private $groupRepo;
	
	/**
	 * @var Core\Repository\CampRepository
	 * @Inject Core\Repository\CampRepository
	 */
	private $campRepo;
	
	
	private $userId = null;
	private $groupId = null;
	private $campId = null;
	
	
	private $context = null;
	
	
	/**
	 * @var Core\Acl\SupportedUserStorage
	 */
	private $storage = null;
	
	/**
	 * @return Core\Acl\SupportedUserStorage
	 */
	public function getSupportedUserStorage()
	{
		if (null === $this->storage) {
			$this->storage = new SupportedUserStorage();
		}
	
		return $this->storage;
	}
	 
	
	
	public function set($userId, $groupId, $campId)
	{
		$this->context = null;
		
		$this->userId = $userId;
		$this->groupId = $groupId;
		$this->campId = $campId;
	}
	
	
	/**
	 * @return CoreApi\Acl\Context
	 */
	public function getContext()
	{
		if(isset($this->context))
		{	return $this->context;	}
		
		$userId =  $this->userId;
		$groupId = $this->groupId;
		$campId =  $this->campId;
		
		
		$me    = $this->getAuthUser();
		$user  = isset($userId)  ? $this->userRepo->find( $userId ) : null;
		$group = isset($groupId) ? $this->groupRepo->find($groupId) : null;
		$camp  = isset($campId)  ? $this->campRepo->find( $campId ) : null;
		
		
		if( $me->getRole() == User::ROLE_ADMIN && 
			!$this->getSupportedUserStorage()->isEmpty()
		){
			$supportedUserId = $this->getSupportedUserStorage()->read();
			$me = $this->userRepo->find($supportedUserId);
		}
		
		$this->context = new Context($me, $user, $group, $camp);
		return $this->context;
	}
	
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function getAuthUser()
	{
		$meId = \Zend_Auth::getInstance()->getIdentity();
		$me = isset($meId) ? $this->userRepo->find(  $meId  ) : null;
		return $me;
	}

}