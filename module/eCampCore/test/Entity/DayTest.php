<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class DayTest extends AbstractTestCase
{
    public function testDay()
    {
        $camp = new Camp();

        $period = new Period();
        $period->setCamp($camp);

        $day = new Day();
        $day->setPeriod($period);
        $day->setDayOffset(0);

        $this->assertEquals($camp, $day->getCamp());
        $this->assertEquals($period, $day->getPeriod());
        $this->assertEquals(0, $day->getDayOffset());
        $this->assertEquals(1, $day->getDayNumber());
    }

}
