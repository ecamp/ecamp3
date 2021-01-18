<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\ContentType\Material\Strategy as MaterialStrategy;
use eCamp\ContentType\Storyboard\Strategy as StoryboardStrategy;
use eCamp\Core\Entity\ContentType;

class ContentTypeTestData extends AbstractFixture {
    public static $TYPE1 = ContentType::class.':TYPE1';
    public static $TYPE_MATERIAL = ContentType::class.':TYPE_MATERIAL';

    public function load(ObjectManager $manager) {
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
    }
}
