<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;
use EcampCore\Entity\EventPrototype;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Event;
use EcampCore\Entity\Plugin;
use EcampCore\Entity\PluginPrototype;
use EcampCore\Entity\PluginInstance;
use EcampCore\Plugin\AbstractStrategy;
use EcampCore\Entity\Medium;

class PluginInstanceTest extends \PHPUnit_Framework_TestCase
{

    public function testPluginInstance()
    {
        $campType = new CampType('name', 'type');
        $eventPrototype = new EventPrototype('event prototype name');

        $plugin = new Plugin('plugin name', 'EcampCoreTest\Entity\PluginStrategy');
        $plugin->setActive(true);

        $camp = new Camp($campType);
        $event = new Event($camp, $eventPrototype);

        $pluginPrototype = new PluginPrototype($plugin, $eventPrototype);
        $pluginInstance = new PluginInstance($event, $pluginPrototype);

        $this->assertEquals($event, $pluginInstance->getEvent());
        $this->assertEquals($camp, $pluginInstance->getCamp());
        $this->assertEquals($pluginPrototype, $pluginInstance->getPluginPrototype());
        $this->assertEquals('plugin name', $pluginInstance->getPluginName());
        $this->assertEquals('EcampCoreTest\Entity\PluginStrategy', $pluginInstance->getPluginStrategyClass());

        $this->assertInstanceOf('EcampCoreTest\Entity\PluginStrategy', $pluginInstance->getStrategyInstance());
    }

}

class PluginStrategy
    extends AbstractStrategy
{
    public function render(Medium $medium)
    {
        return null;
    }

}
