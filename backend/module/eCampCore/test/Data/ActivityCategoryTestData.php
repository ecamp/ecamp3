<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;

class ActivityCategoryTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CATEGORY1 = ActivityCategory::class.':CATEGORY1';
    public static $CATEGORY2 = ActivityCategory::class.':CATEGORY2';

    public function load(ObjectManager $manager) {
        /** @var Camp $camp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        $activityCategoryLS = new ActivityCategory();
        $activityCategoryLS->setCamp($camp);
        $activityCategoryLS->setName('ActivityCategory1');
        $activityCategoryLS->setShort('LS');
        $activityCategoryLS->setColor('#FF9800');
        $activityCategoryLS->setNumberingStyle('i');

        $activityCategoryLA = new ActivityCategory();
        $activityCategoryLA->setCamp($camp);
        $activityCategoryLA->setName('ActivityCategory2');
        $activityCategoryLA->setShort('LA');
        $activityCategoryLA->setColor('#4CAF50');
        $activityCategoryLA->setNumberingStyle('i');

        $manager->persist($activityCategoryLS);
        $manager->persist($activityCategoryLA);
        $manager->flush();

        $this->addReference(self::$CATEGORY1, $activityCategoryLS);
        $this->addReference(self::$CATEGORY2, $activityCategoryLA);
    }

    public function getDependencies() {
        return [CampTestData::class];
    }
}
