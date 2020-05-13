<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\ContentType\Storyboard\Strategy as StoryboardStrategy;
use eCamp\ContentType\Textarea\Strategy as TextareaStrategy;
use eCamp\Core\Entity\ContentType;

class ContentTypeData extends AbstractFixture {
    public static $TEXTAREA = ContentType::class.':TEXTAREA';
    public static $RICHTEXT = ContentType::class.':RICHTEXT';
    public static $STORYBOARD = ContentType::class.':STORYBOARD';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(ContentType::class);

        $contentType = $repository->findOneBy(['name' => 'TextArea']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Textarea');
            $contentType->setActive(true);
            $contentType->setStrategyClass(TextareaStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$TEXTAREA, $contentType);

        $contentType = $repository->findOneBy(['name' => 'RichText']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Richtext');
            $contentType->setActive(true);
            $contentType->setStrategyClass(TextareaStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$RICHTEXT, $contentType);

        $contentType = $repository->findOneBy(['name' => 'storyboard']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Storyboard');
            $contentType->setActive(true);
            $contentType->setStrategyClass(StoryboardStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$STORYBOARD, $contentType);

        $manager->flush();
    }
}
