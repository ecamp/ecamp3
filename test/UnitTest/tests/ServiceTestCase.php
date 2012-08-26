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
	
	
	public function defineContext(
		CoreApi\Entity\User $me = null, 
		CoreApi\Entity\User $user = null, 
		CoreApi\Entity\Group $group = null, 
		CoreApi\Entity\Camp $camp = null
	){
		$meId = $userId = $groupId = $campId = null;
		
		if($me != null){
			$this->em->refresh($me);
			$meId = $me->getId();
		}
		
		if($user != null){
			$this->em->refresh($user);
			$userId = $user->getId();
		}
		
		if($group != null){
			$this->em->refresh($group);
			$groupId = $group->getId();
		}
		
		if($camp != null){
			$this->em->refresh($camp);
			$campId = $camp->getId();
		}
		
		if($meId == null){	
			\Zend_Auth::getInstance()->getStorage()->clear();
		}
		else{
			\Zend_Auth::getInstance()->getStorage()->write($meId);
		}
		
		$this->contextProvider->set($userId, $groupId, $campId);
	}
	
}
