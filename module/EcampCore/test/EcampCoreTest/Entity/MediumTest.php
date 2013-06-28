<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Medium;

class MediumTest extends \PHPUnit_Framework_TestCase
{

	public function testMedium()
	{
		
		$medium = new Medium();
		
		$ref = new \ReflectionClass($medium);
		
		$nameProp = $ref->getProperty('name');
		$nameProp->setAccessible(true);
		$nameProp->setValue($medium, 'web');
		
		$defaultProp = $ref->getProperty('default');
		$defaultProp->setAccessible(true);
		$defaultProp->setValue($medium, true);
		
		
		$this->assertEquals('web', $medium->getName());
		$this->assertTrue($medium->isDefault());
		
	}
    
}
