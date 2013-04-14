<?php

namespace EcampCore\Acl;

use EcampCore\Entity\BaseEntity;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\Camp;


class Context
{
	
	/**
	 * @var EcampCore\Entity\User
	 */
	private $me;
	
	
	/**
	 * @var EcampCore\Entity\User
	 */
	private $user;
	
	
	/**
	 * @var EcampCore\Entity\Group
	 */
	private $group;
	
	
	/**
	 * @var EcampCore\Enitity\Camp
	 */
	private $camp;
	
	
	
	public function __construct(User $me = null, User $user = null, Group $group = null, Camp $camp = null){
		$this->me = $me;
		$this->user = $user;
		$this->group = $group;
		$this->camp = $camp;
	}
	
	
	/**
	 * @return EcampCore\Entity\User
	 */
	public function getMe(){
		return $this->me;
	}
	
	
	/**
	 * @return EcampCore\Entity\User
	 */
	public function getUser(){
		return $this->user;
	}
	
	
	/**
	 * @return EcampCore\Entity\Group
	 */
	public function getGroup(){
		return $this->group;
	}
	
	
	/**
	 * @return EcampCore\Entity\Camp
	 */
	public function getCamp(){
		return $this->camp;
	}
	
	
	/**
	 * @deprecated
	 * Enter description here ...
	 * @param BaseEntity $entity
	 */
	public function Check(BaseEntity $entity)
	{
		
		switch (true)
		{
			case $entity instanceof Period:
				return $this->camp != null && $entity->getCamp() == $this->camp;
				
			case $entity instanceof Day:
				return $this->camp != null && $entity->getCamp() == $this->camp;
			
			case $entity instanceof Event:
				return $this->camp != null && $entity->getCamp() == $this->camp;
				
			case $entity instanceof EventInstance:
				return $this->camp != null && $entity->getCamp() == $this->camp;
				
				
			default:
				return false;
		}
	}
	
	
	public function __toString()
	{
		$ids = array(
			is_null($this->me) 		? 'null' : $this->me->getId(),
			is_null($this->user) 	? 'null' : $this->user->getId(),
			is_null($this->group) 	? 'null' : $this->group->getId(),
			is_null($this->camp) 	? 'null' : $this->camp->getId()
		);
		
		return implode("::", $ids);
	}
	
}
