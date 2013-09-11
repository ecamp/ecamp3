<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\EventType;
use EcampCore\Entity\CampType;

class EventTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testEventType()
    {
        $campType = new CampType('name', 'type');
        $eventType = new EventType($campType);

        $eventType->setName('any event type');
        $eventType->setDefaultColor('any default color');
        $eventType->setDefaultNumberingStyle('i');

        $this->assertEquals('any event type', $eventType->getName());
        $this->assertEquals('any default color', $eventType->getDefaultColor());
        $this->assertEquals('i', $eventType->getDefaultNumberingStyle());
        $this->assertEquals($campType, $eventType->getCampType());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $eventType->getEventPrototypes());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testNumberStyle()
    {
        $campType = new CampType('name', 'type');
        $eventType = new EventType($campType);

        $eventType->setDefaultNumberingStyle('x');
    }

}
