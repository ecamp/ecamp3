<?php
/**
 * Created by JetBrains PhpStorm.
 * User: usu
 * Date: 3/25/11
 * Time: 6:57 PM
 * To change this template use File | Settings | File Templates.
 */
 
class EcampTestCase extends PHPUnit_Framework_TestCase {

	public function setUp(){

		global $application;
		$application->bootstrap();
		
		parent::setUp();
	}

	public function tearDown()
	{
		parent::tearDown();
	}
}
