<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Category;

class CategoryContentTypePrototypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager): void {
        /** @var Category $category */
        $category = $this->getReference(CategoryPrototypeTestData::$PROTOTYPE1);
        $contentType = $this->getReference(ContentTypeTestData::$TYPE1);
        $category->addPreferredContentType($contentType);
        $manager->flush();
    }

    public function getDependencies() {
        return [CategoryPrototypeTestData::class, ContentTypeTestData::class];
    }
}
