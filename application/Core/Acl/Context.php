<?php

namespace Core\Acl;

use Core\Entity\User;
use Core\Entity\Group;
use Core\Entity\Camp;


class Context
{
	
	/**
	 * @var Core\Entity\User
	 */
	private $me;
	
	
	/**
	 * @var Core\Entity\User
	 */
	private $user;
	
	
	/**
	 * @var Core\Entity\Group
	 */
	private $group;
	
	
	/**
	 * @var Core\Enitity\Camp
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
	 * @return Core\Entity\User
	 */
	public function getMe()
	{
		return $this->me;
	}
	
	
	/**
	 * @return Core\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}
	
	
	/**
	 * @return Core\Entity\Group
	 */
	public function getGroup()
	{
		return $this->group;
	}
	
	
	/**
	 * @return Core\Entity\Camp
	 */
	public function getCamp()
	{
		return $this->camp;
	}
	
}
