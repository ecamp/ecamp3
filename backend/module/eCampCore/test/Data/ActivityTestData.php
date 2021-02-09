<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;

class ActivityTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $ACTIVITY1 = Activity::class.':ACTIVITY1';

    public function load(ObjectManager $manager) {
        /** @var Camp $ccamp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        /** @var Category $category */
        $category = $this->getReference(CategoryTestData::$CATEGORY1);

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('Activity1');
        $activity->setCategory($category);

        $manager->persist($activity);
        $manager->flush();

        $this->addReference(self::$ACTIVITY1, $activity);
    }

    public function getDependencies() {
        return [CampTestData::class, CategoryTestData::class];
    }
}
