<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventInstance;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\Period;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class EventInstanceTest extends AbstractTestCase {
    public function testEventInstance() {
        $eventType = new EventType();
        $eventType->setDefaultColor('#1fa2df');
        $eventType->setDefaultNumberingStyle('i');

        $camp = new Camp();

        $start = new \DateTime();
        $end = clone $start;
        $end->add(new \DateInterval('P7D'));

        $period = new Period();
        $period->setCamp($camp);
        $period->setDescription('PeriodDesc');
        $period->setStart($start);
        $period->setEnd($end);

        $eventCategory = new EventCategory();
        $eventCategory->setEventType($eventType);

        $event = new Event();
        $event->setCamp($camp);
        $event->setTitle('EventTitle');
        $event->setEventCategory($eventCategory);

        $eventInstance = new EventInstance();
        $eventInstance->setPeriod($period);
        $eventInstance->setEvent($event);
        $eventInstance->setStart(600);
        $eventInstance->setLength(120);
        $eventInstance->setLeft(0);
        $eventInstance->setWidth(1);
        $period->addEventInstance($eventInstance);

        $eventInstance = new EventInstance();
        $eventInstance->setPeriod($period);
        $eventInstance->setEvent($event);
        $eventInstance->setStart(900);
        $eventInstance->setLength(120);
        $eventInstance->setLeft(0);
        $eventInstance->setWidth(1);
        $period->addEventInstance($eventInstance);

        $this->assertEquals($camp, $eventInstance->getCamp());
        $this->assertEquals($period, $eventInstance->getPeriod());
        $this->assertEquals($event, $eventInstance->getEvent());
        $this->assertEquals($eventCategory, $eventInstance->getEventCategory());
        $this->assertEquals(900, $eventInstance->getStart());
        $this->assertEquals(120, $eventInstance->getLength());
        $this->assertEquals(0, $eventInstance->getLeft());
        $this->assertEquals(1, $eventInstance->getWidth());

        $this->assertEquals(1, $eventInstance->getDayNumber());
        $this->assertEquals('i', $eventInstance->getNumberingStyle());
        $this->assertEquals('1.ii', $eventInstance->getNumber());

        $this->assertEquals('#1fa2df', $eventInstance->getColor());
        $eventCategory->setColor('#FF00FF');
        $this->assertEquals('#FF00FF', $eventInstance->getColor());

        $duration = $eventInstance->getDuration();
        $this->assertEquals('120', $duration->format('%i'));

        $start = $eventInstance->getStartTime();
        $this->assertEquals($period->getStart()->setTime(15, 0, 0), $start);

        $end = $eventInstance->getEndTime();
        $this->assertEquals($period->getStart()->setTime(17, 0, 0), $end);
    }
}
