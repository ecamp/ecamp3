<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\ContentType\Storyboard\Strategy as StoryboardStrategy;
use eCamp\Core\Entity\ContentType;

class ContentTypeTestData extends AbstractFixture {
    public static $TYPE1 = ContentType::class.':TYPE1';

    public function load(ObjectManager $manager) {
        $contentType = new ContentType();
        $contentType->setName('Storyboard');
        $contentType->setAllowMultiple(true);
        $contentType->setStrategyClass(StoryboardStrategy::class);

        $manager->persist($contentType);
        $manager->flush();

        $this->addReference(self::$TYPE1, $contentType);
    }
}
