<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Types\DateUtc;

class PeriodTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $PERIOD1 = Period::class.':PERIOD1';
    public static $DAY1 = Day::class.':DAY1';

    public function load(ObjectManager $manager) {
        /** @var Camp $ccamp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        $period = new Period();
        $period->setCamp($camp);
        $period->setDescription('Period1');
        $period->setStart(new DateUtc('2000-01-01'));
        $period->setEnd(new DateUtc('2000-01-13'));

        $days = $period->getDurationInDays();
        for ($idx = 0; $idx < $days; ++$idx) {
            $day = new Day();
            $day->setPeriod($period);
            $day->setDayOffset($idx);

            if (0 === $idx) {
                $this->addReference(self::$DAY1, $day);
            }

            $manager->persist($day);
        }

        $manager->persist($period);
        $manager->flush();

        $this->addReference(self::$PERIOD1, $period);
    }

    public function getDependencies() {
        return [CampTestData::class];
    }
}
