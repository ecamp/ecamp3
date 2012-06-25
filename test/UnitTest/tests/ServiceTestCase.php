<?php

class ServiceTestCase extends TestCase
{
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	
	/**
	* @var Doctrine\ORM\EntityManager
	* @Inject Doctrine\ORM\EntityManager
	*/
	protected $em;
	
	
	public function setUp()
	{
		parent::setUp();
		
		//$this->clearDatabase();
	}

	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	
	public function defineContext($meId = null, $userId = null, $groupId = null, $campId = null)
	{
		
		if(! is_null($meId))
		//{	\Zend_Auth::getInstance()->getStorage()->clear();	}
		//else
		{	\Zend_Auth::getInstance()->getStorage()->write($meId);	}
		
		$this->contextProvider->set($userId, $groupId, $campId);
	}
	
}
