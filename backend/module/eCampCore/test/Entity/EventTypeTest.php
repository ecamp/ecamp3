<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Plugin;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class EventTypeTest extends AbstractTestCase {
    public function testEventType() {
        $eventType = new EventType();
        $eventType->setName('EventType Name');
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');

        $this->assertEquals('EventType Name', $eventType->getName());
        $this->assertEquals('#FF00FF', $eventType->getDefaultColor());
        $this->assertEquals('i', $eventType->getDefaultNumberingStyle());
        // $this->assertEquals('name', $eventType->getCampTypes()->get(0)->getName());
        // $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventType->getCampTypes());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventType->getEventTypePlugins());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventType->getEventTypeFactories());
    }

    public function testEventTypeFactory() {
        $eventType = new EventType();
        $eventTypeFactory = new EventTypeFactory();

        $this->assertEquals(0, $eventType->getEventTypeFactories()->count());
        $eventType->addEventTypeFactory($eventTypeFactory);
        $this->assertContains($eventTypeFactory, $eventType->getEventTypeFactories());
        $eventType->removeEventTypeFactory($eventTypeFactory);
        $this->assertEquals(0, $eventType->getEventTypeFactories()->count());
    }

    public function testEventTemplate() {
        $eventType = new EventType();
        $eventTemplate = new EventTemplate();

        $this->assertEquals(0, $eventType->getEventTemplates()->count());
        $eventType->addEventTemplate($eventTemplate);
        $this->assertContains($eventTemplate, $eventType->getEventTemplates());
        $eventType->removeEventTemplate($eventTemplate);
        $this->assertEquals(0, $eventType->getEventTemplates()->count());
    }

    public function testEventTypePlugin() {
        $eventType = new EventType();
        $eventTypePlugin = new EventTypePlugin();

        $this->assertEquals(0, $eventType->getEventTypePlugins()->count());
        $eventType->addEventTypePlugin($eventTypePlugin);
        $this->assertContains($eventTypePlugin, $eventType->getEventTypePlugins());
        $eventType->removeEventTypePlugin($eventTypePlugin);
        $this->assertEquals(0, $eventType->getEventTypePlugins()->count());
    }

    public function testCreateEventPlugins() {
        $plugin = new Plugin();
        $plugin->setName('test');

        $eventType = new EventType();
        $eventType->setName('EventType Name');
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');

        $eventTypePlugin = new EventTypePlugin();
        $eventTypePlugin->setPlugin($plugin);
        $eventTypePlugin->setMinNumberPluginInstances(2);
        $eventType->addEventTypePlugin($eventTypePlugin);

        $eventCategory = new EventCategory();
        $eventCategory->setEventType($eventType);

        $event = new Event();
        $event->setEventCategory($eventCategory);

        $eventType->createDefaultEventPlugins($event);

        $this->assertCount(2, $event->getEventPlugins());
    }
}
