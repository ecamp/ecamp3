<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\EventType;
use EcampCore\Entity\CampType;
use EcampCore\Entity\EventCategory;
use EcampCore\Entity\Camp;

class EventCategoryTest extends \PHPUnit_Framework_TestCase
{

    public function testEventCategory()
    {
        $campType = new CampType('name', 'type');

        $eventType = new EventType($campType);
        $eventType->setName('any event type');
        $eventType->setDefaultColor('any default color');
        $eventType->setDefaultNumberingStyle('i');

        $camp = new Camp($campType);
        $eventCategory = new EventCategory($camp, $eventType);
        $eventCategory->setName('any event category name');

        $this->assertEquals($camp, $eventCategory->getCamp());
        $this->assertEquals($eventType, $eventCategory->getEventType());
        $this->assertEquals('any event category name', $eventCategory->getName());

        $this->assertEquals('i', $eventCategory->getNumberingStyle());
        $this->assertEquals('any default color', $eventCategory->getColor());

        $eventType = new EventType($campType);
        $eventType->setName('other event type');
        $eventCategory->setEventType($eventType);
        $eventCategory->setNumberingStyle('a');
        $eventCategory->setColor('specific color');

        $this->assertEquals($eventType, $eventCategory->getEventType());
        $this->assertEquals('a', $eventCategory->getNumberingStyle());
        $this->assertEquals('specific color', $eventCategory->getColor());
    }

    /**
     * @expectedException Exception
     */
    public function testSameCampType()
    {
        $campType = new CampType('name', 'type');
        $eventType = new EventType($campType);
        $camp = new Camp(new CampType('name', 'type'));

        $eventCategory = new EventCategory($camp, $eventType);
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testNumberStyle()
    {
        $campType = new CampType('name', 'type');
        $eventType = new EventType($campType);
        $camp = new Camp($campType);

        $eventCategory = new EventCategory($camp, $eventType);

        $eventCategory->setNumberingStyle('x');
    }

}
