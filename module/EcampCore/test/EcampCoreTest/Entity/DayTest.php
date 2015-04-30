<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Period;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Day;

class DayTest extends \PHPUnit_Framework_TestCase
{

    public static function createDay()
    {
        $camp = CampTest::createUserCamp();

        $period = new Period($camp);
        $period->setDescription('Period.Description');
        $period->setStart(\DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 00:00:00'));
        $period->prePersist();

        $day = new Day($period, 0);
        $day->prePersist();

        return $day;
    }

    public function testDay()
    {
        $day = self::createDay();
        $period = $day->getPeriod();
        $camp = $day->getCamp();

        $this->assertEquals(0, $day->getDayOffset());
        $this->assertEquals($day->getStart(), $period->getStart());
        $this->assertEquals($day->getEnd(), $period->getEnd());

        $this->assertInstanceOf('EcampCore\Entity\Camp', $camp);
        $this->assertInstanceOf('EcampCore\Entity\Period', $period);
        $this->assertInstanceOf('EcampCore\Entity\Story', $day->getStory());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $day->getJobResps());

        $day->preRemove();
        $this->assertNotContains($day, $period->getDays());
    }

}
