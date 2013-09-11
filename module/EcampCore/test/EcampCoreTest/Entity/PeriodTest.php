<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Period;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampType;
use EcampCore\Entity\Day;

class PeriodTest extends \PHPUnit_Framework_TestCase
{

    public function testPeriod()
    {
        $pStart = \DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 00:00:00');
        $pEnd = \DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 23:59:59');

        $campType = new CampType('name', 'type');
        $camp = new Camp($campType);
        $period = new Period($camp);
        $period->setStart($pStart);
        $day = new Day($period, 0);
        $period->getDays()->add($day);
        $period->setDescription('any description');

        $this->assertEquals($camp, $period->getCamp());

        $this->assertEquals($pStart, $period->getStart());
        $this->assertEquals($pEnd, $period->getEnd());
        $this->assertContains($day, $period->getDays());
        $this->assertEquals('any description', $period->getDescription());
        $this->assertEquals(new \DateInterval('P1D'), $period->getDuration());
        $this->assertEquals(1, $period->getNumberOfDays());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $period->getDays());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $period->getEventInstances());
    }

}
