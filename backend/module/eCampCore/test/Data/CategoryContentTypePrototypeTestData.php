<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContentType;

class CategoryContentTypePrototypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $PROTOTYPE1 = CategoryContentTypePrototypeTestData::class.':Prototype1';

    public function load(ObjectManager $manager): void {
        /** @var Category $category */
        $category = $this->getReference(CategoryPrototypeTestData::$PROTOTYPE1);
        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);

        $categoryContentType = new CategoryContentType();
        $categoryContentType->setContentType($contentType);
        $category->addCategoryContentType($categoryContentType);

        $manager->persist($categoryContentType);
        $manager->flush();

        $this->addReference(self::$PROTOTYPE1, $categoryContentType);
    }

    public function getDependencies() {
        return [CategoryPrototypeTestData::class, ContentTypeTestData::class];
    }
}
