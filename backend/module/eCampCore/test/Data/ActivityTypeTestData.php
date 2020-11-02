<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;

class ActivityTypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $TYPE1 = ActivityType::class.':TYPE1';

    public function load(ObjectManager $manager) {
        $activityType = new ActivityType();
        $activityType->setName('ActivityType1');
        $activityType->setDefaultColor('#FF00FF');
        $activityType->setDefaultNumberingStyle('i');

        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $activityTypeContentType = new ActivityTypeContentType();
        $activityTypeContentType->setActivityType($activityType);
        $activityTypeContentType->setContentType($contentType);

        $manager->persist($activityType);
        $manager->persist($activityTypeContentType);
        $manager->flush();

        $this->addReference(self::$TYPE1, $activityType);
    }

    public function getDependencies() {
        return [ContentTypeTestData::class];
    }
}
