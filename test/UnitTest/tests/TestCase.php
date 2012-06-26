<?php

class TestCase extends PHPUnit_Framework_TestCase
{
	
	public function setUp()
	{
		parent::setUp();
		
		global $application;
		$application->bootstrap();
		
		$kernel = \Zend_Registry::get('kernel');
		$kernel->Inject($this);
	}
	
	public function clearDatabase()
	{
		$doctrineContainer = Zend_Registry::get('doctrine');
		$em = $doctrineContainer->getEntityManager();
		
		$sm = new SchemaManager($em);
		$sm->dropAllTables();
		$sm->createSchema();
	}
	
	protected function loadDatabaseDump($file)
	{
		$doctrineContainer = Zend_Registry::get('doctrine');
		$em = $doctrineContainer->getEntityManager();
		
		$sm = new SchemaManager($em);
		$sm->dropAllTables();
		$sm->loadSqlDump($file);
		$sm->updateSchema();
	}
	
	public function tearDown()
	{
		parent::tearDown();
	}
	
}