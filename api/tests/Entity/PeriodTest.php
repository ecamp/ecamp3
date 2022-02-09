<?php

namespace App\Tests\Entity;

use App\Entity\Camp;
use App\Entity\Day;
use App\Entity\Period;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PeriodTest extends TestCase {
    public function setUp(): void {
    }

    public function testFirstDayNumberInPeriod() {
        $camp = new Camp();

        $period1 = new Period();
        $period1->start = new DateTime('2020-09-14');
        $camp->addPeriod($period1);
        $period1->addDay(new Day());
        $period1->addDay(new Day());

        $period2 = new Period();
        $period2->start = new DateTime('2020-08-14');
        $camp->addPeriod($period2);
        $period2->addDay(new Day());
        $period2->addDay(new Day());

        $this->assertEquals(3, $period1->getFirstDayNumber());
        $this->assertEquals(1, $period2->getFirstDayNumber());
    }
}
