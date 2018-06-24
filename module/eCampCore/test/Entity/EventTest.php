<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventInstance;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Plugin;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class EventTest extends AbstractTestCase {
    public function testEventCategory() {
        $camp = new Camp();

        $eventType = new EventType();
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');

        $eventCategory = new EventCategory();
        $eventCategory->setEventType($eventType);

        $event = new Event();
        $event->setCamp($camp);
        $event->setTitle('EventTitle');
        $event->setEventCategory($eventCategory);

        $this->assertEquals($camp, $event->getCamp());
        $this->assertEquals('EventTitle', $event->getTitle());
        $this->assertEquals($eventCategory, $event->getEventCategory());
        $this->assertEquals($eventType, $event->getEventType());
    }

    public function testEventTemplate() {
        $event = new Event();
        $eventPlugin = new EventPlugin();

        $this->assertEquals(0, $event->getEventPlugins()->count());
        $event->addEventPlugin($eventPlugin);
        $this->assertContains($eventPlugin, $event->getEventPlugins());
        $event->removeEventPlugin($eventPlugin);
        $this->assertEquals(0, $event->getEventPlugins()->count());
    }

    public function testEventInstance() {
        $event = new Event();
        $eventInstance = new EventInstance();

        $this->assertEquals(0, $event->getEventInstances()->count());
        $event->addEventInstance($eventInstance);
        $this->assertContains($eventInstance, $event->getEventInstances());
        $event->removeEventInstance($eventInstance);
        $this->assertEquals(0, $event->getEventInstances()->count());
    }

    public function testCreateEventPlugins() {
        $plugin = new Plugin();
        $plugin->setName('TestPlugin');

        $eventTypePlugin = new EventTypePlugin();
        $eventTypePlugin->setPlugin($plugin);
        $eventTypePlugin->setMinNumberPluginInstances(1);

        $camp = new Camp();

        $eventType = new EventType();
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');
        $eventType->addEventTypePlugin($eventTypePlugin);

        $eventCategory = new EventCategory();
        $eventCategory->setEventType($eventType);

        $event = new Event();
        $event->setCamp($camp);
        $event->setTitle('EventTitle');
        $event->setEventCategory($eventCategory);

        $event->PrePersist();

        $this->assertCount(1, $event->getEventPlugins());
    }
}
