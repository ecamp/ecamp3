<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\PluginPosition;
use EcampCore\Entity\EventTemplate;
use EcampCore\Entity\EventPrototype;
use EcampCore\Entity\Medium;
use EcampCore\Entity\PluginPrototype;
use EcampCore\Entity\Plugin;

class PluginPositionTest extends \PHPUnit_Framework_TestCase
{

    public function testPluginPosition()
    {
        $medium = new Medium();
        $plugin = new Plugin('plugin', 'any class');
        $eventPrototype = new EventPrototype('event prototype');
        $eventTemplate = new EventTemplate($eventPrototype, $medium, 'eventTemplate.phtml');

        $pluginPrototype = new PluginPrototype($plugin, $eventPrototype);

        $pluginPos = new PluginPosition($eventTemplate, $pluginPrototype, 'container');

        $this->assertEquals($eventTemplate, $pluginPos->getEventTemplate());
        $this->assertEquals($pluginPrototype, $pluginPos->getPluginPrototype());
        $this->assertEquals('container', $pluginPos->getContainer());

    }

}
