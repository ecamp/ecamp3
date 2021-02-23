<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;

class ScheduleEntryTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $ENTRY1 = ScheduleEntry::class.':ENTRY1';

    public function load(ObjectManager $manager) {
        /** @var Category $category */
        $activity = $this->getReference(ActivityTestData::$ACTIVITY1);

        /** @var Period $period */
        $period = $this->getReference(PeriodTestData::$PERIOD1);

        $scheduleEntry = new ScheduleEntry();
        $scheduleEntry->setActivity($activity);
        $scheduleEntry->setPeriod($period);
        $scheduleEntry->setPeriodOffset(600);
        $scheduleEntry->setLength(120);

        $manager->persist($scheduleEntry);
        $manager->flush();

        $this->addReference(self::$ENTRY1, $scheduleEntry);
    }

    public function getDependencies() {
        return [ActivityTestData::class, PeriodTestData::class];
    }
}
