<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ScheduleEntryTest extends AbstractTestCase {
    public function testScheduleEntry() {
        $activityType = new ActivityType();
        $activityType->setDefaultColor('#1fa2df');
        $activityType->setDefaultNumberingStyle('i');

        $camp = new Camp();

        $start = new \DateTime();
        $end = clone $start;
        $end->add(new \DateInterval('P7D'));

        $period = new Period();
        $period->setCamp($camp);
        $period->setDescription('PeriodDesc');
        $period->setStart($start);
        $period->setEnd($end);

        $activityCategory = new ActivityCategory();
        $activityCategory->setActivityType($activityType);

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('ActivityTitle');
        $activity->setActivityCategory($activityCategory);

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->setPeriod($period);
        $scheduleEntry->setActivity($activity);
        $scheduleEntry->setStart(600);
        $scheduleEntry->setLength(120);
        $scheduleEntry->setLeft(0);
        $scheduleEntry->setWidth(1);
        $period->addScheduleEntry($scheduleEntry);

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->setPeriod($period);
        $scheduleEntry->setActivity($activity);
        $scheduleEntry->setStart(900);
        $scheduleEntry->setLength(120);
        $scheduleEntry->setLeft(0);
        $scheduleEntry->setWidth(1);
        $period->addScheduleEntry($scheduleEntry);

        $this->assertEquals($camp, $scheduleEntry->getCamp());
        $this->assertEquals($period, $scheduleEntry->getPeriod());
        $this->assertEquals($activity, $scheduleEntry->getActivity());
        $this->assertEquals($activityCategory, $scheduleEntry->getActivityCategory());
        $this->assertEquals(900, $scheduleEntry->getStart());
        $this->assertEquals(120, $scheduleEntry->getLength());
        $this->assertEquals(0, $scheduleEntry->getLeft());
        $this->assertEquals(1, $scheduleEntry->getWidth());

        $this->assertEquals(1, $scheduleEntry->getDayNumber());
        $this->assertEquals('i', $scheduleEntry->getNumberingStyle());
        $this->assertEquals('1.ii', $scheduleEntry->getNumber());

        $this->assertEquals('#1fa2df', $scheduleEntry->getColor());
        $activityCategory->setColor('#FF00FF');
        $this->assertEquals('#FF00FF', $scheduleEntry->getColor());

        $duration = $scheduleEntry->getDuration();
        $this->assertEquals('120', $duration->format('%i'));

        $start = $scheduleEntry->getStartTime();
        $this->assertEquals($period->getStart()->setTime(15, 0, 0), $start);

        $end = $scheduleEntry->getEndTime();
        $this->assertEquals($period->getStart()->setTime(17, 0, 0), $end);
    }
}
