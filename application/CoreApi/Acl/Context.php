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
	
	/**
	 * @return arrays
	 */
	public function getRolesinContext()
	{
	
	}
	
	
}
