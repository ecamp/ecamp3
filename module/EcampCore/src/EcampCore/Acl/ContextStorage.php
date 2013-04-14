<?php

namespace EcampCore\Acl;

use EcampCore\Acl\Context;

use EcampCore\Entity\User;

use EcampCore\Repository\CampRepository;
use EcampCore\Repository\GroupRepository;
use EcampCore\Repository\UserRepository;

use Zend\Authentication\AuthenticationService;

class ContextStorage
{
	
	public function __construct(
		AuthenticationService $authService,
		UserRepository $userRepo,
		GroupRepository $groupRepo,
		CampRepository $campRepo
	){
		$this->authService = $authService;
		$this->userRepo = $userRepo;
		$this->groupRepo = $groupRepo;
		$this->campRepo = $campRepo;
	}
	
	/**
	 * @var Zend\Authentication\AuthenticationService
	 */
	private $authService;
	
	/**
	 * @var EcampCore\Repository\UserRepository 
	 */
	private $userRepo;
	
	/**
	 * @var EcampCore\Repository\GroupRepository
	 */
	private $groupRepo;
	
	/**
	 * @var EcampCore\Repository\CampRepository
	 */
	private $campRepo;
	
	
	private $userId = null;
	private $groupId = null;
	private $campId = null;
	
	
	private $context = null;
	
	
	/**
	 * @var EcampCore\Acl\SupportedUserStorage
	 */
	private $storage = null;
	
	/**
	 * @return EcampCore\Acl\SupportedUserStorage
	 */
	public function getSupportedUserStorage(){
		if (null === $this->storage) {
			$this->storage = new SupportedUserStorage();
		}
	
		return $this->storage;
	}
	 
	public function reset(){
		$this->context = null;
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
		
		
		if( $me != null &&
			$me->getRole() == User::ROLE_ADMIN && 
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
		$meId = $this->authService->getIdentity();
		$me = isset($meId) ? $this->userRepo->find(  $meId  ) : null;
		return $me;
	}

}