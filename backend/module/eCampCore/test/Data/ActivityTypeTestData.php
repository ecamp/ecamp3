<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\ContentType\Storyboard\Strategy as StoryboardStrategy;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Entity\ContentType;

class ActivityTypeTestData extends AbstractFixture {
    public static $TYPE1 = ActivityType::class.':TYPE1';

    public function load(ObjectManager $manager) {
        $contentType = new ContentType();
        $contentType->setName('Storyboard');
        $contentType->setAllowMultiple(true);
        $contentType->setStrategyClass(StoryboardStrategy::class);

        $activityType = new ActivityType();
        $activityType->setName('ActivityType1');
        $activityType->setDefaultColor('#FF00FF');
        $activityType->setDefaultNumberingStyle('i');

        $activityTypeContentType = new ActivityTypeContentType();
        $activityTypeContentType->setActivityType($activityType);
        $activityTypeContentType->setContentType($contentType);

        $manager->persist($activityType);
        $manager->persist($contentType);
        $manager->persist($activityTypeContentType);
        $manager->flush();

        $this->addReference(self::$TYPE1, $activityType);
    }
}
