<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Period;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampType;
use EcampCore\Entity\Day;

class DayTest extends \PHPUnit_Framework_TestCase
{

    public function testDay()
    {
        $pStart = \DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 00:00:00');
        $pEnd = \DateTime::createFromFormat('j-M-Y H:i:s', '2-Jan-2000 00:00:00');

        $campType = new CampType('name', 'type');
        $camp = new Camp($campType);
        $period = new Period($camp);
        $period->setStart($pStart);
        $day = new Day($period, 0);
        $day->setNotes('any notes');

        $this->assertEquals(0, $day->getDayOffset());
        $this->assertEquals($period, $day->getPeriod());
        $this->assertEquals($camp, $day->getCamp());

        $this->assertEquals($pStart, $day->getStart());
        $this->assertEquals($pEnd, $day->getEnd());

        $this->assertEquals('any notes', $day->getNotes());

    }

}
