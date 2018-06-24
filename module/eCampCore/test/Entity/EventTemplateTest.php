<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\EventTemplate;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\Medium;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class EventTemplateTest extends AbstractTestCase {
    public function testEventTemplate() {
        $medium = new Medium();

        $eventType = new EventType();
        $eventType->setName('EventType Name');
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');

        $eventTemplate = new EventTemplate();
        $eventTemplate->setEventType($eventType);
        $eventTemplate->setFilename('file.twig');
        $eventTemplate->setMedium($medium);


        $this->assertEquals($eventType, $eventTemplate->getEventType());
        $this->assertEquals('file.twig', $eventTemplate->getFilename());
        $this->assertEquals($medium, $eventTemplate->getMedium());
    }

    public function testEventTemplateContainer() {
        $eventTemplate = new EventTemplate();
        $eventTemplateContainer = new EventTemplateContainer();

        $this->assertEquals(0, $eventTemplate->getEventTemplateContainers()->count());
        $eventTemplate->addEventTemplateContainer($eventTemplateContainer);
        $this->assertContains($eventTemplateContainer, $eventTemplate->getEventTemplateContainers());
        $eventTemplate->removeEventTemplateContainer($eventTemplateContainer);
        $this->assertEquals(0, $eventTemplate->getEventTemplateContainers()->count());
    }
}
