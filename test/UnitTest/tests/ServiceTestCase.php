<?php

class ServiceTestCase extends TestCase
{
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	
	public function setUp()
	{
		parent::setUp();
		
		$this->clearDatabase();
		$this->createDatabase();
	}

	
	public function tearDown()
	{
		parent::tearDown();
	}
	
	
	public function clearDatabase()
	{
		$metadatas = $this->em->getMetadataFactory()->getAllMetadata();
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		$schemaTool->dropSchema($metadatas);
	}
	
	
	public function createDatabase(){
		$metadatas = $this->em->getMetadataFactory()->getAllMetadata();
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		$schemaTool->createSchema($metadatas);
	}
	
	
	public function defineContext($meId = null, $userId = null, $groupId = null, $campId = null)
	{
		print_r($meId);
		if($meId != null)
		{	\Zend_Auth::getInstance()->getStorage()->clear();	}
		else
		{	\Zend_Auth::getInstance()->getStorage()->write($meId);	}
		
		$this->contextProvider->set($userId, $groupId, $campId);
	}
	
}
