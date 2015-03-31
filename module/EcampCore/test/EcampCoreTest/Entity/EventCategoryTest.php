<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\EventType;
use EcampCore\Entity\CampType;
use EcampCore\Entity\EventCategory;
use EcampCore\Entity\Camp;

use Exception;
use OutOfRangeException;

class EventCategoryTest extends \PHPUnit_Framework_TestCase
{

    private function createEventCategory()
    {
        $campType = new CampType('name', 'type');

        $eventType = new EventType();
        $eventType->setName('EventType Name');
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');
        $eventType->getCampTypes()->add($campType);

        $camp = new Camp();
        $camp->setCampType($campType);

        $eventCategory = new EventCategory($camp, $eventType);
        $eventCategory->setShort('EC');
        $eventCategory->setName('EventCategory Name');

        return $eventCategory;
    }


    public function testEventCategory()
    {
        $eventCategory = $this->createEventCategory();

        $this->assertEquals('EC', $eventCategory->getShort());
        $this->assertEquals('EventCategory Name', $eventCategory->getName());
        $this->assertEquals('i', $eventCategory->getNumberingStyle());
        $this->assertEquals('#FF00FF', $eventCategory->getColor());
        $this->assertEquals('EventType Name', $eventCategory->getEventType()->getName());

        $eventCategory->setColor('#EEEEEE');
        $this->assertEquals('#000000', $eventCategory->getTextColor());

        $eventCategory->setColor('#111111');
        $this->assertEquals('#FFFFFF', $eventCategory->getTextColor());


        $eventCategory->setNumberingStyle('1');
        $this->assertEquals('2', $eventCategory->getStyledNumber(2));

        $eventCategory->setNumberingStyle('a');
        $this->assertEquals('ab', $eventCategory->getStyledNumber(28));

        $eventCategory->setNumberingStyle('A');
        $this->assertEquals('B', $eventCategory->getStyledNumber(2));

        $eventCategory->setNumberingStyle('i');
        $this->assertEquals('ii', $eventCategory->getStyledNumber(2));

        $eventCategory->setNumberingStyle('I');
        $this->assertEquals('II', $eventCategory->getStyledNumber(2));

    }

    /**
     * @expectedException Exception
     */
    public function testSameCampType()
    {
        $campType = new CampType('name', 'type');
        $eventType = new EventType();
        $eventType->getCampTypes()->add($campType);
        $camp = new Camp();
        $camp->setCampType(new CampType('name', 'type'));

        new EventCategory($camp, $eventType);
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testNumberStyle()
    {
        $this->createEventCategory()->setNumberingStyle('x');
    }

}
