<?php

namespace App\Tests\Entity;

use App\Entity\Activity;
use App\Entity\Category;
use App\Entity\Day;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
class ScheduleEntryTest extends TestCase {
    private ScheduleEntry $scheduleEntry1;
    private ScheduleEntry $scheduleEntry2;
    private ScheduleEntry $scheduleEntry3;
    private Period $period;
    private Day $day1;
    private Day $day2;

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

        $this->scheduleEntry2 = new ScheduleEntry();
        $this->scheduleEntry2->periodOffset = 960;
        $this->scheduleEntry2->length = 90;
        $this->period->addScheduleEntry($this->scheduleEntry2);

        $this->scheduleEntry3 = new ScheduleEntry();
        $this->scheduleEntry3->periodOffset = 2400;
        $this->scheduleEntry3->length = 90;
        $this->period->addScheduleEntry($this->scheduleEntry3);

        $this->scheduleEntry1 = new ScheduleEntry();
        $this->scheduleEntry1->periodOffset = 420;
        $this->scheduleEntry1->length = 30;
        $this->period->addScheduleEntry($this->scheduleEntry1);
    }

    public function testGetStart() {
        $this->assertEquals(new DateTime('2020-07-14T16:00:00+00:00'), $this->scheduleEntry2->getStart());
        $this->assertEquals(new DateTime('2020-07-15T16:00:00+00:00'), $this->scheduleEntry3->getStart());
    }

    public function testGetStartReturnsNullOnError() {
        $this->period->start = null;
        $this->assertNull($this->scheduleEntry2->getStart());
        $this->assertNull($this->scheduleEntry3->getStart());
    }

    public function testGetEnd() {
        $this->assertEquals(new DateTime('2020-07-14T17:30:00+00:00'), $this->scheduleEntry2->getEnd());
        $this->assertEquals(new DateTime('2020-07-15T17:30:00+00:00'), $this->scheduleEntry3->getEnd());
    }

    public function testGetEndReturnsNullOnError() {
        $this->period->start = null;
        $this->assertNull($this->scheduleEntry2->getEnd());
        $this->assertNull($this->scheduleEntry3->getEnd());
    }

    public function testGetDayNumber() {
        $this->assertEquals(1, $this->scheduleEntry1->getDayNumber());
        $this->assertEquals(1, $this->scheduleEntry2->getDayNumber());
        $this->assertEquals(2, $this->scheduleEntry3->getDayNumber());
    }

    public function testGetScheduleEntryNumber() {
        $this->assertEquals(1, $this->scheduleEntry1->getScheduleEntryNumber());
        $this->assertEquals(2, $this->scheduleEntry2->getScheduleEntryNumber());
        $this->assertEquals(1, $this->scheduleEntry3->getScheduleEntryNumber());
    }

    public function testGetNumber() {
        $this->assertEquals('1.1', $this->scheduleEntry1->getNumber());
        $this->assertEquals('1.2', $this->scheduleEntry2->getNumber());
        $this->assertEquals('2.1', $this->scheduleEntry3->getNumber());
    }

    public function testGetNumberInDifferentNumberingStyle() {
        $category = new Category();
        $category->numberingStyle = 'i';
        $activity = new Activity();
        $activity->category = $category;
        $this->scheduleEntry2->activity = $activity;
        $this->assertEquals('1.1', $this->scheduleEntry1->getNumber());
        $this->assertEquals('1.i', $this->scheduleEntry2->getNumber());
        $this->assertEquals('2.1', $this->scheduleEntry3->getNumber());
    }

    public function testGetNumberOrdersSamePeriodOffsetByLeft() {
        $this->scheduleEntry1->periodOffset = $this->scheduleEntry2->periodOffset;
        $this->scheduleEntry1->left = 0.5;
        $this->scheduleEntry2->left = 0;

        $this->assertEquals('1.1', $this->scheduleEntry2->getNumber());
        $this->assertEquals('1.2', $this->scheduleEntry1->getNumber());
    }

    public function testGetNumberOrdersSamePeriodOffsetAndLeftByCreateTime() {
        $this->scheduleEntry1->periodOffset = $this->scheduleEntry2->periodOffset;
        $this->scheduleEntry1->left = 0;
        $this->scheduleEntry2->left = 0;
        $this->setCreateTime($this->scheduleEntry1, new DateTime('yesterday'));
        $this->setCreateTime($this->scheduleEntry2, new DateTime('now'));

        $this->assertEquals('1.1', $this->scheduleEntry1->getNumber());
        $this->assertEquals('1.2', $this->scheduleEntry2->getNumber());
    }

    public function testGetDay() {
        $this->assertEquals($this->day1, $this->scheduleEntry2->getDay());
        $this->assertEquals($this->day2, $this->scheduleEntry3->getDay());
    }

    protected function setCreateTime(ScheduleEntry $scheduleEntry, DateTime $createTime) {
        $createTimeProperty = (new ReflectionClass(ScheduleEntry::class))->getProperty('createTime');
        $createTimeProperty->setAccessible(true);
        $createTimeProperty->setValue($scheduleEntry, $createTime);
        $createTimeProperty->setAccessible(false);
    }
}
