<?php

namespace EcampCore\RepositoryTraits;

trait UserGroupRepositoryTrait
{
	/**
	 * @var EcampCore\Repository\UserGroupRepository
	 */
	private $userGroupRepository;
	
	/**
	 * @return EcampCore\Repository\UserGroupRepository
	 */
	public function getUserGroupRepository(){
		return $this->userGroupRepository;
	}
	
	public function setUserGroupRepository($userGroupRepository){
		$this->userGroupRepository = $userGroupRepository;
		return $this;
	}
}
