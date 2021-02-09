<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CategoryContentTemplate;
use eCamp\Core\Entity\CategoryTemplate;

class CategoryContentTemplateTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $TEMPLATE1 = CategoryContentTemplateTestData::class.':Template1';

    public function load(ObjectManager $manager) {
        /** @var CategoryTemplate $categoryTemplate */
        $categoryTemplate = $this->getReference(CategoryTemplateTestData::$TEMPLATE1);
        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $categoryContentTemplate = new CategoryContentTemplate();
        $categoryContentTemplate->setContentType($contentType);
        $categoryTemplate->addCategoryContentTemplate($categoryContentTemplate);

        $manager->persist($categoryContentTemplate);
        $manager->flush();

        $this->addReference(self::$TEMPLATE1, $categoryContentTemplate);
    }

    public function getDependencies() {
        return [CategoryTemplateTestData::class, ContentTypeTestData::class];
    }
}
