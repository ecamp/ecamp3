<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\Camp;

class ActivityCategoryTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CATEGORY1 = ActivityCategory::class.':CATEGORY1';

    public function load(ObjectManager $manager) {
        /** @var Camp $camp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        /** @var ActivityType $activityType */
        $activityType = $this->getReference(ActivityTypeTestData::$TYPE1);

        $activityCategory = new ActivityCategory();
        $activityCategory->setCamp($camp);
        $activityCategory->setActivityType($activityType);
        $activityCategory->setName('ActivityCategory1');
        $activityCategory->setShort('AC');
        $activityCategory->setColor('#4CAF50');

        $manager->persist($activityCategory);
        $manager->flush();

        $this->addReference(self::$CATEGORY1, $activityCategory);
    }

    public function getDependencies() {
        return [CampTestData::class];
    }
}
