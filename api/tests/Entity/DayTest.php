<?php

namespace App\Tests\Entity;

use App\Entity\Day;
use App\Entity\Period;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DayTest extends TestCase {
    private Day $day1;
    private Day $day2;
    private Period $period;

    public function setUp(): void {
        parent::setUp();
        $this->day1 = new Day();
        $this->day1->dayOffset = 0;
        $this->day2 = new Day();
        $this->day2->dayOffset = 1;

        $this->period = new Period();
        $this->period->start = new DateTime('2020-07-14');
        $this->period->addDay($this->day1);
        $this->period->addDay($this->day2);
    }

    public function testGetStart() {
        $this->assertEquals(new DateTime('2020-07-14T00:00:00+00:00'), $this->day1->getStart());
        $this->assertEquals(new DateTime('2020-07-15T00:00:00+00:00'), $this->day2->getStart());
    }

    public function testGetStartReturnsNullOnError() {
        $this->period->start = null;
        $this->assertNull($this->day1->getStart());
        $this->assertNull($this->day2->getStart());
    }

    public function testGetEnd() {
        $this->assertEquals(new DateTime('2020-07-15T00:00:00+00:00'), $this->day1->getEnd());
        $this->assertEquals(new DateTime('2020-07-16T00:00:00+00:00'), $this->day2->getEnd());
    }

    public function testGetEndReturnsNullOnError() {
        $this->period->start = null;
        $this->assertNull($this->day1->getEnd());
        $this->assertNull($this->day2->getEnd());
    }
}
