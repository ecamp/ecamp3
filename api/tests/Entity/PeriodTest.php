<?php

namespace App\Tests\Entity;

use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\SingleText;
use App\Entity\Day;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
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

    public function testGetContentNodes() {
        $camp = new Camp();

        $period = new Period();
        $period->start = new DateTime('2020-09-14');
        $camp->addPeriod($period);
        $scheduleEntry1 = new ScheduleEntry();
        $scheduleEntry2 = new ScheduleEntry();
        $scheduleEntry3 = new ScheduleEntry();
        $period->addScheduleEntry($scheduleEntry1);
        $period->addScheduleEntry($scheduleEntry2);
        $period->addScheduleEntry($scheduleEntry3);

        $activity1 = new Activity();
        $columnLayout1 = new ColumnLayout();
        $singleText1 = new SingleText();
        $columnLayout1->addRootDescendant($singleText1);
        $columnLayout1->addChild($singleText1);
        $activity1->setRootContentNode($columnLayout1);
        $scheduleEntry1->activity = $activity1;
        $scheduleEntry2->activity = $activity1;

        $activity2 = new Activity();
        $columnLayout2 = new ColumnLayout();
        $singleText2 = new SingleText();
        $columnLayout2->addRootDescendant($singleText2);
        $columnLayout2->addChild($singleText2);
        $activity2->setRootContentNode($columnLayout2);
        $scheduleEntry3->activity = $activity2;

        $this->assertEquals(
            [$singleText1, $columnLayout1, $singleText2, $columnLayout2],
            $period->getContentNodes()
        );
    }
}
