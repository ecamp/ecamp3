<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;

class PeriodTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $PERIOD1 = Period::class.':PERIOD1';

    public function load(ObjectManager $manager) {
        /** @var Camp $ccamp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        $period = new Period();
        $period->setCamp($camp);
        $period->setDescription('Period1');
        $period->setStart(new \DateTime('2000-01-01'));
        $period->setEnd(new \DateTime('2000-01-03'));

        $manager->persist($period);
        $manager->flush();

        $this->addReference(self::$PERIOD1, $period);
    }

    public function getDependencies() {
        return [CampTestData::class];
    }
}
