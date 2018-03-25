<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\EventTemplate;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Medium;
use eCamp\Core\Entity\Plugin;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class EventTemplateContainerTest extends AbstractTestCase
{
    public function testEventTemplateContainer()
    {
        $plugin = new Plugin();
        $medium = new Medium();

        $eventType = new EventType();
        $eventType->setName('EventType Name');
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');

        $eventTemplate = new EventTemplate();
        $eventTemplate->setEventType($eventType);
        $eventTemplate->setFilename('file.twig');
        $eventTemplate->setMedium($medium);

        $eventTypePlugin = new EventTypePlugin();
        $eventTypePlugin->setEventType($eventType);
        $eventTypePlugin->setPlugin($plugin);
        $eventTypePlugin->setMinNumberPluginInstances(1);
        $eventTypePlugin->setMaxNumberPluginInstances(3);

        $eventTemplateContainer = new EventTemplateContainer();
        $eventTemplateContainer->setEventTemplate($eventTemplate);
        $eventTemplateContainer->setEventTypePlugin($eventTypePlugin);
        $eventTemplateContainer->setFilename('container.twig');
        $eventTemplateContainer->setContainerName('containername');


        $this->assertEquals($eventTemplate, $eventTemplateContainer->getEventTemplate());
        $this->assertEquals($eventTypePlugin, $eventTemplateContainer->getEventTypePlugin());
        $this->assertEquals('container.twig', $eventTemplateContainer->getFilename());
        $this->assertEquals('containername', $eventTemplateContainer->getContainerName());

    }

}