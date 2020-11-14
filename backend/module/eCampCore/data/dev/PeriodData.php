<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Core\Types\DateUTC;

class PeriodData extends AbstractFixture implements DependentFixtureInterface {
    public static $PERIOD_1 = Period::class.':PERIOD_1';
    public static $PERIOD_2 = Period::class.':PERIOD_2';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Period::class);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_1);

        $period = $repository->findOneBy(['camp' => $camp, 'description' => 'Period of Camp1']);
        if (null == $period) {
            $start = new DateUTC();
            $end = clone $start;
            $end->add(new \DateInterval('P7D'));
            $period = new Period();
            $period->setCamp($camp);
            $period->setDescription('Period of Camp1');
            $period->setStart($start);
            $period->setEnd($end);

            $manager->persist($period);

            $days = $period->getDurationInDays();
            for ($idx = 0; $idx < $days; ++$idx) {
                $day = new Day();
                $day->setPeriod($period);
                $day->setDayOffset($idx);

                $manager->persist($day);
            }
        }
        $this->addReference(self::$PERIOD_1, $period);

        /** @var Camp $camp */
        $camp = $this->getReference(CampData::$CAMP_2);

        $period = $repository->findOneBy(['camp' => $camp, 'description' => 'Period of Camp2']);
        if (null == $period) {
            $start = new DateUTC();
            $end = clone $start;
            $end->add(new \DateInterval('P5D'));

            $period = new Period();
            $period->setCamp($camp);
            $period->setDescription('Period of Camp2');
            $period->setStart($start);
            $period->setEnd($end);

            $manager->persist($period);

            $days = $period->getDurationInDays();
            for ($idx = 0; $idx < $days; ++$idx) {
                $day = new Day();
                $day->setPeriod($period);
                $day->setDayOffset($idx);

                $manager->persist($day);
            }
        }
        $this->addReference(self::$PERIOD_2, $period);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampData::class];
    }
}
