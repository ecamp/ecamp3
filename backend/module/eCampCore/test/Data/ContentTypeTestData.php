<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\ContentType\ColumnLayout\Strategy as ColumnLayoutStrategy;
use eCamp\ContentType\Material\Strategy as MaterialStrategy;
use eCamp\ContentType\SingleText\Strategy as SingleTextStrategy;
use eCamp\ContentType\Storyboard\Strategy as StoryboardStrategy;
use eCamp\Core\Entity\ContentType;

class ContentTypeTestData extends AbstractFixture {
    public static $TYPE_COLUMN_LAYOUT = ContentType::class.':COLUMN_LAYOUT';
    public static $TYPE1 = ContentType::class.':TYPE1';
    public static $TYPE_MATERIAL = ContentType::class.':TYPE_MATERIAL';
    public static $TYPE_SINGLE_TEXT = ContentType::class.':SINGLE_TEXT';

    public function load(ObjectManager $manager): void {
        $contentType = new ContentType();
        $contentType->setName('Storyboard');
        $contentType->setStrategyClass(StoryboardStrategy::class);

        $manager->persist($contentType);
        $manager->flush();

        $this->addReference(self::$TYPE1, $contentType);

        $contentType = new ContentType();
        $contentType->setName('Material');
        $contentType->setStrategyClass(MaterialStrategy::class);

        $manager->persist($contentType);
        $manager->flush();

        $this->addReference(self::$TYPE_MATERIAL, $contentType);

        $contentType = new ContentType();
        $contentType->setName('ColumnLayout');
        $contentType->setStrategyClass(ColumnLayoutStrategy::class);

        $manager->persist($contentType);
        $manager->flush();

        $this->addReference(self::$TYPE_COLUMN_LAYOUT, $contentType);

        $contentType = new ContentType();
        $contentType->setName('SingleText');
        $contentType->setStrategyClass(SingleTextStrategy::class);

        $manager->persist($contentType);
        $manager->flush();

        $this->addReference(self::$TYPE_SINGLE_TEXT, $contentType);
    }
}
