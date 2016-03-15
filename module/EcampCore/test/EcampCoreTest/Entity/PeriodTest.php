<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Period;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Day;

class PeriodTest extends \PHPUnit_Framework_TestCase
{

    public static function createPeriod()
    {
        $camp = CampTest::createUserCamp();

        $period = new Period($camp);
        $period->setDescription('Period.Description');

        $period->getDays()->add(new Day($period, 0));
        $period->getDays()->add(new Day($period, 1));

        return $period;
    }

    public function testPeriod()
    {
        $pStart1 = \DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 00:00:00');
        $pStart2 = \DateTime::createFromFormat('j-M-Y H:i:s', '31-Jan-2000 00:00:00');
        $pStart3 = \DateTime::createFromFormat('j-M-Y H:i:s', '31-Dec-2000 00:00:00');

        $period = $this->createPeriod();
        $camp = $period->getCamp();

        $this->assertEquals('Camp.Name', $camp->getName());

        $this->assertNull($period->getStart());
        $this->assertNull($period->getEnd());

        $period->setStart($pStart1);
        $this->assertEquals('01. - 02.01.2000', $period->getRange());

        $period->setStart($pStart2);
        $this->assertEquals('31.01. - 01.02.2000', $period->getRange());

        $period->setStart($pStart3);
        $this->assertEquals('31.12.2000 - 01.01.2001', $period->getRange());

        $period->prePersist();
        $this->assertContains($period, $camp->getPeriods());

        $period->preRemove();
        $this->assertNotContains($period, $camp->getPeriods());

        $this->assertCount(2, $period->getDays());
        $this->assertEquals(0, $period->getDay(0)->getDayOffset());
        $this->assertEquals('Period.Description', $period->getDescription());
        $this->assertEquals(new \DateInterval('P2D'), $period->getDuration());
        $this->assertEquals(2, $period->getNumberOfDays());

        $this->assertInstanceOf('EcampCore\Entity\Story', $period->getStory());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $period->getDays());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $period->getEventInstances());
    }

}
