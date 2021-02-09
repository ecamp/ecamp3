<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CategoryContentTypeTemplate;
use eCamp\Core\Entity\CategoryTemplate;

class CategoryContentTypeTemplateTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $TEMPLATE1 = CategoryContentTypeTemplateTestData::class.':Template1';

    public function load(ObjectManager $manager) {
        /** @var CategoryTemplate $categoryTemplate */
        $categoryTemplate = $this->getReference(CategoryTemplateTestData::$TEMPLATE1);
        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $categoryContentTypeTemplate = new CategoryContentTypeTemplate();
        $categoryContentTypeTemplate->setContentType($contentType);
        $categoryTemplate->addCategoryContentTypeTemplate($categoryContentTypeTemplate);

        $manager->persist($categoryContentTypeTemplate);
        $manager->flush();

        $this->addReference(self::$TEMPLATE1, $categoryContentTypeTemplate);
    }

    public function getDependencies() {
        return [CategoryTemplateTestData::class, ContentTypeTestData::class];
    }
}
