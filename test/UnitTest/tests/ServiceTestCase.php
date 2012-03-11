<?php

class ServiceTestCase extends TestCase
{
		
	public function setUp()
	{
		parent::setUp();
		
		$this->clearDatabase();
	}

	
	public function tearDown()
	{
		parent::tearDown();
	}
	
}
