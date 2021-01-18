<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ContentTypeConfig;

class ContentTypeConfigTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CONFIG1 = ContentTypeConfigTestData::class.':Config1';

    public function load(ObjectManager $manager) {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = $this->getReference(ActivityCategoryTestData::$CATEGORY1);
        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $contentTypeConfig = new ContentTypeConfig();
        $contentTypeConfig->setContentType($contentType);
        $contentTypeConfig->setRequired(true);
        $contentTypeConfig->setMultiple(true);
        $activityCategory->addContentTypeConfig($contentTypeConfig);

        $manager->persist($contentTypeConfig);
        $manager->flush();

        $this->addReference(self::$CONFIG1, $contentTypeConfig);
    }

    public function getDependencies() {
        return [ActivityCategoryTestData::class, ContentTypeTestData::class];
    }
}
