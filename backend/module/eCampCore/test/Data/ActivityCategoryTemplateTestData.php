<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategoryTemplate;

class ActivityCategoryTemplateTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $TEMPLATE1 = ActivityCategoryTemplate::class.':Template1';

    public function load(ObjectManager $manager) {
        $campTemplate = $this->getReference(CampTemplateTestData::$TEMPLATE1);

        $activityCategoryTemplate = new ActivityCategoryTemplate();
        $activityCategoryTemplate->setShort('AC');
        $activityCategoryTemplate->setName('ActivityCategory1');
        $activityCategoryTemplate->setColor('#FF00FF');
        $activityCategoryTemplate->setNumberingStyle('i');
        $campTemplate->addActivityCategoryTemplate($activityCategoryTemplate);

        $manager->persist($activityCategoryTemplate);
        $manager->flush();

        $this->addReference(self::$TEMPLATE1, $activityCategoryTemplate);
    }

    public function getDependencies() {
        return [CampTemplateTestData::class];
    }
}
