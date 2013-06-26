<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\EventPrototype;

class EventPrototypeTest extends \PHPUnit_Framework_TestCase
{

	public function testEventPrototype()
	{
		$eventPrototype = new EventPrototype('event prototype name');
		
		$this->assertEquals('event prototype name', $eventPrototype->getName());
		
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventPrototype->getEventTemplates());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventPrototype->getPluginPrototypes());
		
	}
    
}
