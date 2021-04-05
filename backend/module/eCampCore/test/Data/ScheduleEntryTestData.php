<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Lib\Types\EDateInterval;

class ScheduleEntryTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $ENTRY1 = ScheduleEntry::class.':ENTRY1';
    public static $ENTRY2 = ScheduleEntry::class.':ENTRY2';

    public function load(ObjectManager $manager): void {
        /** @var Activity $activity */
        $activity = $this->getReference(ActivityTestData::$ACTIVITY1);

        /** @var Period $period */
        $period = $this->getReference(PeriodTestData::$PERIOD1);

        $scheduleEntry1 = new ScheduleEntry();
        $scheduleEntry1->setActivity($activity);
        $scheduleEntry1->setPeriod($period);
        $scheduleEntry1->setPeriodOffset(EDateInterval::ofHours(10)->getTotalMinutes());
        $scheduleEntry1->setLength(EDateInterval::ofHours(2)->getTotalMinutes());

        $manager->persist($scheduleEntry1);
        $manager->flush();

        $this->addReference(self::$ENTRY1, $scheduleEntry1);

        $scheduleEntry2 = new ScheduleEntry();
        $scheduleEntry2->setActivity($activity);
        $scheduleEntry2->setPeriod($period);
        $scheduleEntry2->setPeriodOffset(EDateInterval::ofDays(5)->getTotalMinutes());
        $scheduleEntry2->setLength(EDateInterval::ofHours(2)->getTotalMinutes());

        $manager->persist($scheduleEntry2);
        $manager->flush();

        $this->addReference(self::$ENTRY2, $scheduleEntry2);
    }

    public function getDependencies() {
        return [ActivityTestData::class, PeriodTestData::class];
    }
}
