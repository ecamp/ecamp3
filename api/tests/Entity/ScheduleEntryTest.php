<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\Category;
use App\Entity\Day;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use PHPUnit\Framework\TestCase;

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
        $this->period->start = new \DateTimeImmutable('2020-07-14');
        $this->period->addDay($this->day1);
        $this->period->addDay($this->day2);

        $camp = new Camp();
        $camp->addPeriod($this->period);

        $this->scheduleEntry2 = new ScheduleEntry();
        $this->scheduleEntry2->startOffset = 960;
        $this->scheduleEntry2->endOffset = 960 + 90;
        $this->period->addScheduleEntry($this->scheduleEntry2);
        $reflection = new \ReflectionClass($this->scheduleEntry2);
        $property = $reflection->getProperty('createTime');
        $date = new \DateTimeImmutable('2026-01-01 10:00:00');
        $property->setValue($this->scheduleEntry2, $date);

        $this->scheduleEntry3 = new ScheduleEntry();
        $this->scheduleEntry3->startOffset = 2400;
        $this->scheduleEntry3->endOffset = 2400 + 90;
        $this->period->addScheduleEntry($this->scheduleEntry3);

        $this->scheduleEntry1 = new ScheduleEntry();
        $this->scheduleEntry1->startOffset = 420;
        $this->scheduleEntry1->endOffset = 420 + 30;
        $this->period->addScheduleEntry($this->scheduleEntry1);
        $reflection = new \ReflectionClass($this->scheduleEntry1);
        $property = $reflection->getProperty('createTime');
        $date = new \DateTimeImmutable('2026-11-11 10:00:00');
        $property->setValue($this->scheduleEntry1, $date);
    }

    public function testGetStart() {
        $this->assertEquals(new \DateTimeImmutable('2020-07-14T16:00:00+00:00'), $this->scheduleEntry2->getStart());
        $this->assertEquals(new \DateTimeImmutable('2020-07-15T16:00:00+00:00'), $this->scheduleEntry3->getStart());
    }

    public function testGetStartReturnsNullOnError() {
        $this->period->start = null;
        $this->assertNull($this->scheduleEntry2->getStart());
        $this->assertNull($this->scheduleEntry3->getStart());
    }

    public function testGetEnd() {
        $this->assertEquals(new \DateTimeImmutable('2020-07-14T17:30:00+00:00'), $this->scheduleEntry2->getEnd());
        $this->assertEquals(new \DateTimeImmutable('2020-07-15T17:30:00+00:00'), $this->scheduleEntry3->getEnd());
    }

    public function testGetEndReturnsNullOnError() {
        $this->period->start = null;
        $this->assertNull($this->scheduleEntry2->getEnd());
        $this->assertNull($this->scheduleEntry3->getEnd());
    }

    public function testGetDayNumber() {
        $this->assertSame(1, $this->scheduleEntry1->getDayNumber());
        $this->assertSame(1, $this->scheduleEntry2->getDayNumber());
        $this->assertSame(2, $this->scheduleEntry3->getDayNumber());
    }

    public function testGetScheduleEntryNumber() {
        $this->assertSame(1, $this->scheduleEntry1->getScheduleEntryNumber());
        $this->assertSame(2, $this->scheduleEntry2->getScheduleEntryNumber());
        $this->assertSame(1, $this->scheduleEntry3->getScheduleEntryNumber());
    }

    public function testGetNumber() {
        $this->assertSame('1.1', $this->scheduleEntry1->getNumber());
        $this->assertSame('1.2', $this->scheduleEntry2->getNumber());
        $this->assertSame('2.1', $this->scheduleEntry3->getNumber());
    }

    public function testGetNumberInDifferentNumberingStyle() {
        $category = new Category();
        $category->numberingStyle = 'i';
        $activity = new Activity();
        $activity->category = $category;
        $this->scheduleEntry2->activity = $activity;
        $this->assertSame('1.1', $this->scheduleEntry1->getNumber());
        $this->assertSame('1.i', $this->scheduleEntry2->getNumber());
        $this->assertSame('2.1', $this->scheduleEntry3->getNumber());
    }

    public function testGetEmptyValueWhenNumberingStyleIsNone() {
        $category = new Category();
        $category->numberingStyle = '-';
        $activity = new Activity();
        $activity->category = $category;
        $this->scheduleEntry2->activity = $activity;
        $this->assertSame('1.1', $this->scheduleEntry1->getNumber());
        $this->assertSame('', $this->scheduleEntry2->getNumber());
        $this->assertSame('2.1', $this->scheduleEntry3->getNumber());
    }

    public function testGetNumberOrdersSamePeriodOffsetByLeft() {
        $this->scheduleEntry1->startOffset = $this->scheduleEntry2->startOffset;
        $this->scheduleEntry1->left = 0.5;
        $this->scheduleEntry2->left = 0;

        $this->assertSame('1.1', $this->scheduleEntry2->getNumber());
        $this->assertSame('1.2', $this->scheduleEntry1->getNumber());
    }

    public function testGetNumberOrdersSamePeriodOffsetAndLeftByLength() {
        $this->scheduleEntry1->startOffset = $this->scheduleEntry2->startOffset;
        $this->scheduleEntry1->left = $this->scheduleEntry2->left;
        $this->scheduleEntry1->endOffset = $this->scheduleEntry1->startOffset + 60;
        $this->scheduleEntry2->endOffset = $this->scheduleEntry1->startOffset + 120;

        $this->assertSame('1.1', $this->scheduleEntry2->getNumber());
        $this->assertSame('1.2', $this->scheduleEntry1->getNumber());
    }

    public function testGetNumberOrdersSamePeriodOffsetAndLeftAndLengthByCreateTime() {
        $this->scheduleEntry1->startOffset = $this->scheduleEntry2->startOffset;
        $this->scheduleEntry1->left = $this->scheduleEntry2->left;
        $this->scheduleEntry1->endOffset = $this->scheduleEntry2->endOffset;

        $this->assertSame('1.1', $this->scheduleEntry2->getNumber());
        $this->assertSame('1.2', $this->scheduleEntry1->getNumber());
    }

    public function testGetNumberOrdersSamePeriodOffsetAndLeftAndLengthAndCreateTimeById() {
        $this->scheduleEntry1->startOffset = $this->scheduleEntry2->startOffset;
        $this->scheduleEntry1->left = $this->scheduleEntry2->left;
        $this->scheduleEntry1->endOffset = $this->scheduleEntry2->endOffset;

        $reflection = new \ReflectionClass($this->scheduleEntry2);
        $property = $reflection->getProperty('createTime');
        $property->setValue($this->scheduleEntry1, $property->getValue($this->scheduleEntry2));

        if ($this->scheduleEntry1->getId() < $this->scheduleEntry2->getId()) {
            $this->assertSame('1.1', $this->scheduleEntry1->getNumber());
            $this->assertSame('1.2', $this->scheduleEntry2->getNumber());
        } else {
            $this->assertSame('1.1', $this->scheduleEntry2->getNumber());
            $this->assertSame('1.2', $this->scheduleEntry1->getNumber());
        }
    }

    public function testGetDay() {
        $this->assertEquals($this->day1, $this->scheduleEntry2->getDay());
        $this->assertEquals($this->day2, $this->scheduleEntry3->getDay());
    }

    public function testGetDayReturnsNullIfDayEntityIsMissing() {
        // given
        $this->period->removeDay($this->day1);

        // when
        $day = $this->scheduleEntry1->getDay();

        // then
        $this->assertNull($day);
    }

    protected function setCreateTime(ScheduleEntry $scheduleEntry, \DateTimeImmutable $createTime) {
        $createTimeProperty = new \ReflectionClass(ScheduleEntry::class)->getProperty('createTime');
        $createTimeProperty->setValue($scheduleEntry, $createTime);
    }
}
