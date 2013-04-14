<?php

namespace EcampCore\Acl;


class ContextProvider
{
	
	/**
	 * @param ContextStorage $contextStorage
	 */
	public function __construct(ContextStorage $contextStorage){
		$this->contextStorage = $contextStorage;
	}
	
	/**
	 * @var Core\Acl\ContextStorage
	 */
	private $contextStorage;
	
	
	/**
	 * @param integer $userId
	 * @param integer $groupId
	 * @param integer $campId
	 */
	public function set($userId, $groupId, $campId)
	{
		$this->contextStorage->set($userId, $groupId, $campId);
	}
	
	/**
	 * 
	 */
	public function reset()
	{
		$this->contextStorage->reset();
	}
	
	/**
	 * @return CoreApi\Acl\Context
	 */
	public function getContext()
	{
		return $this->contextStorage->getContext();
	}
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function getUser(){
		return $this->getContext()->getUser();
	}
	
	/**
	 * @return CoreApi\Entity\Camp
	 */
	public function getCamp(){
		return $this->getContext()->getCamp();
	}
	
	/**
	 * @return CoreApi\Entity\Group
	 */
	public function getGroup(){
		return $this->getContext()->getGroup();
	}
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function getMe(){
		return $this->getContext()->getMe();
	}
	
}