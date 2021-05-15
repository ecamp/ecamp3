<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;

class ContentNodeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $CATEGORY_CONTENT1 = ContentNodeTestData::class.':CategoryContent1';
    public static $CATEGORY_CONTENT1_CAMP2 = ContentNodeTestData::class.':CategoryContent1Camp2';

    public function load(ObjectManager $manager): void {
        /** @var Category $category */
        $category = $this->getReference(CategoryTestData::$CATEGORY1);
        /** @var ContentType $storyboard */
        $storyboard = $this->getReference(ContentTypeTestData::$TYPE1);
        $contentNode = new ContentNode();
        $contentNode->setInstanceName('Programm');
        $contentNode->setContentType($storyboard);
        $category->setRootContentNode($contentNode);

        /** @var Category $category */
        $category1Camp2 = $this->getReference(CategoryTestData::$CATEGORY1_CAMP2);
        /** @var ContentType $columnLayout */
        $columnLayout = $this->getReference(ContentTypeTestData::$TYPE_COLUMN_LAYOUT);
        $contentNode1Camp2 = new ContentNode();
        $contentNode1Camp2->setJsonConfig(['columns' => [['slot' => '1', 'width' => 12]]]);
        $contentNode1Camp2->setContentType($columnLayout);
        $category1Camp2->setRootContentNode($contentNode1Camp2);

        $manager->persist($contentNode);
        $manager->persist($contentNode1Camp2);
        $manager->flush();

        $this->addReference(self::$CATEGORY_CONTENT1, $contentNode);
        $this->addReference(self::$CATEGORY_CONTENT1_CAMP2, $contentNode1Camp2);
    }

    public function getDependencies() {
        return [CategoryTestData::class, ContentTypeTestData::class];
    }
}
