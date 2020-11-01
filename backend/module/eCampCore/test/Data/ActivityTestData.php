<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;

class ActivityTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $ACTIVITY1 = Activity::class.':ACTIVITY1';

    public function load(ObjectManager $manager) {
        /** @var Camp $ccamp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        /** @var ActivityCategory $category */
        $category = $this->getReference(ActivityCategoryTestData::$CATEGORY1);

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('Activity1');
        $activity->setActivityCategory($category);

        $manager->persist($activity);
        $manager->flush();

        $this->addReference(self::$ACTIVITY1, $activity);
    }

    public function getDependencies() {
        return [CampTestData::class, ActivityCategoryTestData::class];
    }
}
