<?php

class TestCase extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	
	public function setUp()
	{
		parent::setUp();
		
		global $application;
		$application->bootstrap();
		
		$kernel = \Zend_Registry::get('kernel');
		$kernel->Inject($this);
		
	}
	
	
	public function tearDown()
	{
		parent::tearDown();
	}
	
}