<?php

class ServiceTestCase extends TestCase
{
	
	/**
	 * @var CoreApi\Acl\ContextManager
	 * @Inject CoreApi\Acl\ContextManager
	 */
	protected $contextManager;
	
	
	/**
	* @var Doctrine\ORM\EntityManager
	* @Inject Doctrine\ORM\EntityManager
	*/
	protected $em;
	
	
	public function setUp()
	{
		parent::setUp();
		
		$this->clearDatabase();
	}

	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	
	public function defineContext($meId = null, $userId = null, $groupId = null, $campId = null)
	{
		
		if(is_null($meId))
		{	\Zend_Auth::getInstance()->clearIdentity();	}
		else
		{	\Zend_Auth::getInstance()->getStorage()->write($meId);	}
		
		$this->contextManager->set($userId, $groupId, $campId);
	}
	
}
