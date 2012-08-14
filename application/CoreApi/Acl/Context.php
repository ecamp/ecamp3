<?php

namespace CoreApi\Acl;

use CoreApi\Entity\User;
use CoreApi\Entity\Group;
use CoreApi\Entity\Camp;


class Context
{
	
	/**
	 * @var CoreApi\Entity\User
	 */
	private $me;
	
	
	/**
	 * @var CoreApi\Entity\User
	 */
	private $user;
	
	
	/**
	 * @var CoreApi\Entity\Group
	 */
	private $group;
	
	
	/**
	 * @var CoreApi\Enitity\Camp
	 */
	private $camp;
	
	
	
	public function __construct(User $me = null, User $user = null, Group $group = null, Camp $camp = null)
	{
		$this->me = $me;
		$this->user = $user;
		$this->group = $group;
		$this->camp = $camp;
	}
	
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function getMe()
	{
		return $this->me;
	}
	
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}
	
	
	/**
	 * @return CoreApi\Entity\Group
	 */
	public function getGroup()
	{
		return $this->group;
	}
	
	
	/**
	 * @return CoreApi\Entity\Camp
	 */
	public function getCamp()
	{
		return $this->camp;
	}
	
	
	protected function Check(BaseEntity $entity)
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
