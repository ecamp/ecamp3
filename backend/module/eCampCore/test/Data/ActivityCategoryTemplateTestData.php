<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\ContentTypeConfigTemplate;

class ActivityCategoryTemplateTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $TYPE1 = ActivityCategoryTemplate::class.':TYPE1';

    public function load(ObjectManager $manager) {
        $campTemplate = $this->getReference(CampTemplateTestData::$TYPE1);

        $activityCategoryTemplate = new ActivityCategoryTemplate();
        $activityCategoryTemplate->setName('ActivityType1');
        $activityCategoryTemplate->setColor('#FF00FF');
        $activityCategoryTemplate->setNumberingStyle('i');
        $campTemplate->addActivityCategoryTemplate($activityCategoryTemplate);

        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $contentTypeConfigTemplate = new ContentTypeConfigTemplate();
        $contentTypeConfigTemplate->setContentType($contentType);
        $contentTypeConfigTemplate->setRequired(true);
        $contentTypeConfigTemplate->setMultiple(true);
        $activityCategoryTemplate->addContentTypeConfigTemplate($contentTypeConfigTemplate);

        $manager->persist($activityCategoryTemplate);
        $manager->persist($contentTypeConfigTemplate);
        $manager->flush();

        $this->addReference(self::$TYPE1, $activityCategoryTemplate);
    }

    public function getDependencies() {
        return [CampTemplateTestData::class, ContentTypeTestData::class];
    }
}
