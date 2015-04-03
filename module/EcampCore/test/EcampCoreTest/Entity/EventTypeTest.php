<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\EventType;
use EcampCore\Entity\CampType;
use OutOfRangeException;

class EventTypeTest extends \PHPUnit_Framework_TestCase
{

    private function createEventType()
    {
        $campType = new CampType('name', 'type');
        $eventType = new EventType();
        $eventType->getCampTypes()->add($campType);
        $eventType->setName('EventType Name');
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');

        return $eventType;
    }

    public function testEventType()
    {
        $eventType = $this->createEventType();

        $this->assertEquals('EventType Name', $eventType->getName());
        $this->assertEquals('#FF00FF', $eventType->getDefaultColor());
        $this->assertEquals('i', $eventType->getDefaultNumberingStyle());

        $this->assertEquals('name', $eventType->getCampTypes()->get(0)->getName());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventType->getCampTypes());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventType->getEventTypePlugins());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventType->getEventTypeFactories());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testNumberStyle()
    {
        $this->createEventType()->setDefaultNumberingStyle('x');
    }

}
