<?php

namespace CoreApi\Acl;


class ContextProvider
{
	
	/**
	 * @var Core\Acl\ContextStorage
	 * @Inject Core\Acl\ContextStorage
	 */
	protected $contextStorage;
	
	
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
	 * @return CoreApi\Acl\Context
	 */
	public function getContext()
	{
		return $this->contextStorage->getContext();
	}
	
}