<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Plugin;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class EventPluginTest extends AbstractTestCase {
    public function testEventPlugin() {
        $camp = new Camp();

        $plugin = new Plugin();

        $eventType = new EventType();
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');

        $eventTypePlugin = new EventTypePlugin();
        $eventTypePlugin->setEventType($eventType);
        $eventTypePlugin->setPlugin($plugin);
        $eventTypePlugin->setMinNumberPluginInstances(1);
        $eventTypePlugin->setMaxNumberPluginInstances(3);

        $event = new Event();
        $event->setCamp($camp);
        $event->setTitle('EventTitle');

        $eventPlugin = new EventPlugin();
        $eventPlugin->setEvent($event);
        $eventPlugin->setEventTypePlugin($eventTypePlugin);
        $eventPlugin->setInstanceName('EventPluginName');

        $this->assertEquals($event, $eventPlugin->getEvent());
        $this->assertEquals($eventTypePlugin, $eventPlugin->getEventTypePlugin());
        $this->assertEquals('EventPluginName', $eventPlugin->getInstanceName());
        $this->assertEquals($plugin, $eventPlugin->getPlugin());
    }
}
