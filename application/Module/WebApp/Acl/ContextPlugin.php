<?php

namespace WebApp\Acl;

use Core\Context;

class ContextPlugin extends \Zend_Controller_Plugin_Abstract
{
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	
	public function preDispatch(\Zend_Controller_Request_Abstract $request)
	{
		$userId  = $request->getParam('user');
		$groupId = $request->getParam('group');
		$campId  = $request->getParam('camp');
		
		$this->contextProvider->set($userId, $groupId, $campId);
	}
	
}