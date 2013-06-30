<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;
use EcampCore\Entity\EventType;
use EcampCore\Entity\EventPrototype;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventInstance;
use EcampCore\Entity\Period;
use EcampCore\Entity\Day;

class EventInstanceTest extends \PHPUnit_Framework_TestCase
{

    public function testEventInstance()
    {
        $pStart = \DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 00:00:00');
        $eiStart = \DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 10:00:00');
        $eiEnd   = \DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 12:00:00');

        $campType = new CampType('name', 'type');
        $eventType = new EventType($campType);
        $eventPrototype = new EventPrototype('any event prototype');
        $eventType->getEventPrototypes()->add($eventPrototype);

        $camp = new Camp($campType);
        $period = new Period($camp);
        $period->setStart($pStart);

        $day = new Day($period, 0);

        $event = new Event($camp, $eventPrototype);
        $event->setTitle('any title');

        $eventInstance = new EventInstance($event);
        $eventInstance->setPeriod($period);
        $eventInstance->setOffset(new \DateInterval('PT10H'));
        $eventInstance->setDuration(new \DateInterval('PT2H'));

        $this->assertEquals($camp, $eventInstance->getCamp());
        $this->assertEquals($period, $eventInstance->getPeriod());
        $this->assertEquals($event, $eventInstance->getEvent());

        $this->assertEquals(new \DateInterval('PT600M'), $eventInstance->getOffset());
        $this->assertEquals(600, $eventInstance->getOffsetInMinutes());

        $this->assertEquals(new \DateInterval('PT120M'), $eventInstance->getDuration());
        $this->assertEquals(120, $eventInstance->getDurationInMinutes());

        $this->assertEquals($eiStart, $eventInstance->getStartTime());
        $this->assertEquals($eiEnd, $eventInstance->getEndTime());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $event->getEventInstances());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $event->getPluginInstances());
    }

}
