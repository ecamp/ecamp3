<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContentType;

class CategoryContentTypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CATEGORY_CONTENT_TYPE1 = CategoryContentTypeTestData::class.':CategoryContentType1';

    public function load(ObjectManager $manager) {
        /** @var Category $category */
        $category = $this->getReference(CategoryTestData::$CATEGORY1);
        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $categoryContentType = new CategoryContentType();
        $categoryContentType->setContentType($contentType);
        $category->addCategoryContentType($categoryContentType);

        $manager->persist($categoryContentType);
        $manager->flush();

        $this->addReference(self::$CATEGORY_CONTENT_TYPE1, $categoryContentType);
    }

    public function getDependencies() {
        return [CategoryTestData::class, ContentTypeTestData::class];
    }
}
