<?php

namespace eCamp\CoreData\Dev;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;

class ScheduleEntryData extends AbstractFixture implements DependentFixtureInterface {
    public static $SCHEDULE_ENTRY_1_LS_1 = Activity::class.':SCHEDULE_ENTRY_1_LS_1';
    public static $SCHEDULE_ENTRY_1_LS_2 = Activity::class.':SCHEDULE_ENTRY_1_LS_2';
    public static $SCHEDULE_ENTRY_1_LA_1 = Activity::class.':SCHEDULE_ENTRY_1_LA_1';
    public static $SCHEDULE_ENTRY_1_LA_2 = Activity::class.':SCHEDULE_ENTRY_1_LA_2';
    public static $SCHEDULE_ENTRY_2_LS = Activity::class.':SCHEDULE_ENTRY_2_LS';
    public static $SCHEDULE_ENTRY_2_LA = Activity::class.':SCHEDULE_ENTRY_2_LA';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(ScheduleEntry::class);

        /** @var Period $period */
        $period = $this->getReference(PeriodData::$PERIOD_1);

        /** @var Activity $activity */
        $activity = $this->getReference(ActivityData::$EVENT_1_LS);

        $scheduleEntries = $repository->findBy(['period' => $period, 'activity' => $activity]);
        $scheduleEntry = array_shift($scheduleEntries);
        if (null == $scheduleEntry) {
            $scheduleEntry = new ScheduleEntry();
            $scheduleEntry->setPeriod($period);
            $scheduleEntry->setActivity($activity);
            $scheduleEntry->setPeriodOffset(600);
            $scheduleEntry->setLength(120);
            $scheduleEntry->setLeft(0);
            $scheduleEntry->setWidth(1);

            $manager->persist($scheduleEntry);
        }
        $this->addReference(self::$SCHEDULE_ENTRY_1_LS_1, $scheduleEntry);

        $scheduleEntry = array_shift($scheduleEntries);
        if (null == $scheduleEntry) {
            $scheduleEntry = new ScheduleEntry();
            $scheduleEntry->setPeriod($period);
            $scheduleEntry->setActivity($activity);
            $scheduleEntry->setPeriodOffset(2040);
            $scheduleEntry->setLength(180);
            $scheduleEntry->setLeft(0);
            $scheduleEntry->setWidth(1);

            $manager->persist($scheduleEntry);
        }
        $this->addReference(self::$SCHEDULE_ENTRY_1_LS_2, $scheduleEntry);

        /** @var Activity $activity */
        $activity = $this->getReference(ActivityData::$EVENT_1_LA);

        $scheduleEntries = $repository->findBy(['period' => $period, 'activity' => $activity]);
        $scheduleEntry = array_shift($scheduleEntries);
        if (null == $scheduleEntry) {
            $scheduleEntry = new ScheduleEntry();
            $scheduleEntry->setPeriod($period);
            $scheduleEntry->setActivity($activity);
            $scheduleEntry->setPeriodOffset(900);
            $scheduleEntry->setLength(150);
            $scheduleEntry->setLeft(0);
            $scheduleEntry->setWidth(1);

            $manager->persist($scheduleEntry);
        }
        $this->addReference(self::$SCHEDULE_ENTRY_1_LA_1, $scheduleEntry);

        $scheduleEntry = array_shift($scheduleEntries);
        if (null == $scheduleEntry) {
            $scheduleEntry = new ScheduleEntry();
            $scheduleEntry->setPeriod($period);
            $scheduleEntry->setActivity($activity);
            $scheduleEntry->setPeriodOffset(2340);
            $scheduleEntry->setLength(120);
            $scheduleEntry->setLeft(0);
            $scheduleEntry->setWidth(1);

            $manager->persist($scheduleEntry);
        }
        $this->addReference(self::$SCHEDULE_ENTRY_1_LA_2, $scheduleEntry);

        /** @var Period $period */
        $period = $this->getReference(PeriodData::$PERIOD_2);

        /** @var Activity $activity */
        $activity = $this->getReference(ActivityData::$EVENT_2_LS);

        $scheduleEntries = $repository->findBy(['period' => $period, 'activity' => $activity]);
        $scheduleEntry = array_shift($scheduleEntries);
        if (null == $scheduleEntry) {
            $scheduleEntry = new ScheduleEntry();
            $scheduleEntry->setPeriod($period);
            $scheduleEntry->setActivity($activity);
            $scheduleEntry->setPeriodOffset(600);
            $scheduleEntry->setLength(120);
            $scheduleEntry->setLeft(0);
            $scheduleEntry->setWidth(1);

            $manager->persist($scheduleEntry);
        }
        $this->addReference(self::$SCHEDULE_ENTRY_2_LS, $scheduleEntry);

        /** @var Activity $activity */
        $activity = $this->getReference(ActivityData::$EVENT_2_LA);

        $scheduleEntries = $repository->findBy(['period' => $period, 'activity' => $activity]);
        $scheduleEntry = array_shift($scheduleEntries);
        if (null == $scheduleEntry) {
            $scheduleEntry = new ScheduleEntry();
            $scheduleEntry->setPeriod($period);
            $scheduleEntry->setActivity($activity);
            $scheduleEntry->setPeriodOffset(900);
            $scheduleEntry->setLength(90);
            $scheduleEntry->setLeft(0);
            $scheduleEntry->setWidth(1);

            $manager->persist($scheduleEntry);
        }
        $this->addReference(self::$SCHEDULE_ENTRY_2_LA, $scheduleEntry);

        $manager->flush();
    }

    public function getDependencies() {
        return [PeriodData::class, ActivityData::class];
    }
}
