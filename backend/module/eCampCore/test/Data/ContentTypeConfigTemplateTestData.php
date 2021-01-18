<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ContentTypeConfigTemplate;

class ContentTypeConfigTemplateTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $TEMPLATE1 = ContentTypeConfigTemplateTestData::class.':Template1';

    public function load(ObjectManager $manager) {
        $activityCategoryTemplate = $this->getReference(ActivityCategoryTemplateTestData::$TEMPLATE1);
        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $contentTypeConfigTemplate = new ContentTypeConfigTemplate();
        $contentTypeConfigTemplate->setContentType($contentType);
        $contentTypeConfigTemplate->setRequired(true);
        $contentTypeConfigTemplate->setMultiple(true);
        $activityCategoryTemplate->addContentTypeConfigTemplate($contentTypeConfigTemplate);

        $manager->persist($contentTypeConfigTemplate);
        $manager->flush();

        $this->addReference(self::$TEMPLATE1, $contentTypeConfigTemplate);
    }

    public function getDependencies() {
        return [ActivityCategoryTemplateTestData::class, ContentTypeTestData::class];
    }
}
