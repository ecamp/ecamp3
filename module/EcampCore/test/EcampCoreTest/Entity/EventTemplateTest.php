<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\EventPrototype;
use EcampCore\Entity\EventTemplate;
use EcampCore\Entity\Medium;

class EventTemplateTest extends \PHPUnit_Framework_TestCase
{

	public function testEventTemplate()
	{
		$medium = new Medium();
		$eventPrototype = new EventPrototype('event prototype name');
		$eventTemplate = new EventTemplate($eventPrototype, $medium, 'template.phtml');
		
		$this->assertEquals('template.phtml', $eventTemplate->getFilename());
		$this->assertEquals($medium, $eventTemplate->getMedium());
		$this->assertEquals($eventPrototype, $eventTemplate->getEventPrototype());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventTemplate->getPluginPositions());
	}
    
}
