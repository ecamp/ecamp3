<?php

class ServiceTestCase extends PHPUnit_Framework_TestCase
{
	
	private $schemaManager;
	
	public function setUp()
	{
		$this->schemaManager = \Zend_Registry::get('SchemaManager');
		
		
	}

	
	public function tearDown()
	{
	}
	
	
	
	private function clearDatabase()
	{
		$this->schemaManager->clearAllTables();
	}
	
	
	protected function loadDatabaseDump($file)
	{
		$this->schemaManager->runSqlFile($file);
	}
	
	
}
