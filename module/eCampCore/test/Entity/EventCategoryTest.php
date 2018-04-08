<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class EventCategoryTest extends AbstractTestCase
{
    public function testEventCategory()
    {
        $camp = new Camp();

        $eventType = new EventType();
        $eventType->setDefaultColor('#FF00FF');
        $eventType->setDefaultNumberingStyle('i');

        $eventCategory = new EventCategory();
        $eventCategory->setEventType($eventType);
        $eventCategory->setCamp($camp);
        $eventCategory->setName('TestCategory');
        $eventCategory->setShort('TC');

        $this->assertEquals($eventType, $eventCategory->getEventType());
        $this->assertEquals($camp, $eventCategory->getCamp());
        $this->assertEquals('TestCategory', $eventCategory->getName());
        $this->assertEquals('TC', $eventCategory->getShort());
        $this->assertEquals('#FF00FF', $eventCategory->getColor());
        $this->assertEquals('i', $eventCategory->getNumberingStyle());
    }

    public function testNumberingStyle()
    {
        $eventCategory = new EventCategory();

        $this->assertEquals('31', $eventCategory->getStyledNumber(31));

        $eventCategory->setNumberingStyle('a');
        $this->assertEquals('ae', $eventCategory->getStyledNumber(31));

        $eventCategory->setNumberingStyle('A');
        $this->assertEquals('AE', $eventCategory->getStyledNumber(31));

        $eventCategory->setNumberingStyle('i');
        $this->assertEquals('xxxi', $eventCategory->getStyledNumber(31));

        $eventCategory->setNumberingStyle('I');
        $this->assertEquals('XXXI', $eventCategory->getStyledNumber(31));
    }
}
