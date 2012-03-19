<?php

namespace CoreApi\Acl;


class ContextManager
{
	
	
	/**
	 * @var Core\Acl\ContextStorage
	 * @Inject Core\Acl\ContextStorage
	 */
	private $contextStorage;
	
	
	
	public function set($userId, $groupId, $campId)
	{
		$this->contextStorage->set($userId, $groupId, $campId);
	}
	
	
}