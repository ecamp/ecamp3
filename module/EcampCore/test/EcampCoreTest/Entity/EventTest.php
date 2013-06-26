<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;
use EcampCore\Entity\EventType;
use EcampCore\Entity\EventPrototype;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Event;
use EcampCore\Entity\Plugin;
use EcampCore\Entity\PluginPrototype;
use EcampCore\Entity\PluginInstance;

class EventTest extends \PHPUnit_Framework_TestCase
{

	public function testEvent()
	{
		$campType = new CampType();
		$eventType = new EventType($campType);
		$eventPrototype = new EventPrototype('any event prototype');
		$eventType->getEventPrototypes()->add($eventPrototype);
		
		
		$plugin = new Plugin('plugin', 'strategy class');
		$plugin->setActive(true);
		
		$pluginPrototype = new PluginPrototype($plugin, $eventPrototype);
		$eventPrototype->getPluginPrototypes()->add($pluginPrototype);
		$pluginPrototype->setActive(1);
				
		$camp = new Camp($campType);
		$event = new Event($camp, $eventPrototype);
		$event->setTitle('any title');
		
		$this->assertEquals(1, $event->countPluginsByPrototype($pluginPrototype));
		
		$plugins = $event->getPluginsByPrototype($pluginPrototype);
		$this->assertCount(1, $plugins);
		
		$plugin = $plugins[0];
		$this->assertEquals($pluginPrototype, $plugin->getPluginPrototype());
		
		
		$this->assertEquals($camp, $event->getCamp());
		$this->assertEquals($eventPrototype, $event->getEventPrototype());
		$this->assertEquals('any title', $event->getTitle());
		
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $event->getEventInstances());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $event->getPluginInstances());
	}
	
	
    
}
