<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\DayHydrator;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Types\DateUtc;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class DayHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        $camp = new Camp();
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');

        $period = new Period();
        $period->setCamp($camp);
        $period->setDescription('desc');
        $period->setStart(new DateUtc('2000-01-01'));
        $period->setEnd(new DateUtc('2000-01-03'));

        $day = new Day();
        $day->setPeriod($period);
        $day->setDayOffset(0);

        $activity = new Activity();
        $activity->setCamp($camp);

        $scheduleEntry1 = new ScheduleEntry();
        $scheduleEntry1->setPeriod($period);
        $scheduleEntry1->setPeriodOffset(0);
        $activity->addScheduleEntry($scheduleEntry1);
        $period->addScheduleEntry($scheduleEntry1);

        $scheduleEntry2 = new ScheduleEntry();
        $scheduleEntry2->setPeriod($period);
        $scheduleEntry2->setPeriodOffset(24 * 60);
        $activity->addScheduleEntry($scheduleEntry2);
        $period->addScheduleEntry($scheduleEntry2);

        $hydrator = new DayHydrator();
        $data = $hydrator->extract($day);

        $this->assertEquals(0, $data['dayOffset']);
        $this->assertEquals(1, $data['number']);

        /** @var EntityLinkCollection $scheduleEntries */
        $scheduleEntries = $data['scheduleEntries'];
        $this->assertEquals(1, $scheduleEntries->getTotalItemCount());
    }

    public function testHydrate(): void {
        $camp = new Camp();

        $period = new Period();
        $period->setCamp($camp);

        $day = new Day();
        $day->setPeriod($period);

        $data = [
            'dayOffset' => 0,
        ];

        $hydrator = new DayHydrator();
        $hydrator->hydrate($data, $day);

        $this->assertEquals(0, $day->getDayOffset());
        $this->assertEquals(1, $day->getDayNumber());
    }
}
