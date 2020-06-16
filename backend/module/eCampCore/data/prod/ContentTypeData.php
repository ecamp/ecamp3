<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\ContentType\Richtext\Strategy as RichtextStrategy;
use eCamp\ContentType\Storyboard\Strategy as StoryboardStrategy;
use eCamp\ContentType\Textarea\Strategy as TextareaStrategy;
use eCamp\Core\Entity\ContentType;

class ContentTypeData extends AbstractFixture {
    public static $TEXTAREA = ContentType::class.':TEXTAREA';
    public static $RICHTEXT = ContentType::class.':RICHTEXT';
    public static $STORYBOARD = ContentType::class.':STORYBOARD';
    public static $STORYCONTEXT = ContentType::class.':STORYCONTEXT';
    public static $SIKO = ContentType::class.':SIKO';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(ContentType::class);

        // Story board (Programmablauf)
        $contentType = $repository->findOneBy(['name' => 'Storyboard']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Storyboard');
            $contentType->setAllowMultiple(true);
            $contentType->setStrategyClass(StoryboardStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$STORYBOARD, $contentType);

        // Story context (Roter Faden, Einkleidung, ...)
        // implemented with a simple text field
        $contentType = $repository->findOneBy(['name' => 'StoryContext']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Storycontext');
            $contentType->setStrategyClass(TextareaStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$STORYCONTEXT, $contentType);

        // SiKo
        $contentType = $repository->findOneBy(['name' => 'SiKo']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('SiKo');
            $contentType->setStrategyClass(RichtextStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$SIKO, $contentType);

        $manager->flush();
    }
}
