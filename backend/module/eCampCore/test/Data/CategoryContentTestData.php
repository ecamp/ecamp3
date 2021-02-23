<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContent;

class CategoryContentTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CATEGORY_CONTENT1 = CategoryContentTestData::class.':CategoryContent1';

    public function load(ObjectManager $manager) {
        /** @var Category $category */
        $category = $this->getReference(CategoryTestData::$CATEGORY1);
        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $categoryContent = new CategoryContent();
        $categoryContent->setContentType($contentType);
        $category->addCategoryContent($categoryContent);

        $manager->persist($categoryContent);
        $manager->flush();

        $this->addReference(self::$CATEGORY_CONTENT1, $categoryContent);
    }

    public function getDependencies() {
        return [CategoryTestData::class, ContentTypeTestData::class];
    }
}
