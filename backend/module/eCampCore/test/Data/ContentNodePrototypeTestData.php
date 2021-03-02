<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;

class ContentNodePrototypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CATEGORY_CONTENT1 = ContentNodePrototypeTestData::class.':CategoryContent1';

    public function load(ObjectManager $manager): void {
        /** @var Category $category */
        $category = $this->getReference(CategoryPrototypeTestData::$PROTOTYPE1);
        /** @var ContentType $storyboard */
        $storyboard = $this->getReference(ContentTypeTestData::$TYPE1);

        $contentNode = new ContentNode();
        $contentNode->setInstanceName('Programm');
        $contentNode->setContentType($storyboard);
        $category->setRootContentNode($contentNode);

        $manager->persist($contentNode);
        $manager->flush();

        $this->addReference(self::$CATEGORY_CONTENT1, $contentNode);
    }

    public function getDependencies() {
        return [CategoryPrototypeTestData::class, ContentTypeTestData::class];
    }
}
