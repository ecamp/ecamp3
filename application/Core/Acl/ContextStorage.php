<?php

namespace Core\Acl;


use CoreApi\Acl\Context;

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
		
		
		$meId = \Zend_Auth::getInstance()->getIdentity();
		
		$userId =  $this->userId;
		$groupId = $this->groupId;
		$campId =  $this->campId;
		
		$me = 	 isset($meId) ? $this->userRepo->find(  $meId  ) : null;
		$user =  isset($userId)  ? $this->userRepo->find( $userId ) : null;
		$group = isset($groupId) ? $this->groupRepo->find($groupId) : null;
		$camp =  isset($campId)  ? $this->campRepo->find( $campId ) : null;
		
		$this->context = new Context($me, $user, $group, $camp);
		return $this->context;
	}

}