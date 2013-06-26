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

class PluginTest extends \PHPUnit_Framework_TestCase
{

	public function testPlugin()
	{
		$plugin = new Plugin('plugin name', 'plugin strategy class');
		$plugin->setActive(true);
		
		$this->assertEquals('plugin name', $plugin->getName());
		$this->assertEquals('plugin strategy class', $plugin->getStrategyClass());
		
		$this->assertTrue($plugin->getActive());
		
	}
	
	
    
}
