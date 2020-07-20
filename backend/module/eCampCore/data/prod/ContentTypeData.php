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
    public static $STORYCONTEXT = ContentType::class.':STORYCONTEXT';
    public static $NOTES = ContentType::class.':NOTES';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(ContentType::class);

        // Story board (Programmablauf)
        $contentType = $repository->findOneBy(['name' => 'storyboard']);
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

        // Notes (Notizen)
        $contentType = $repository->findOneBy(['name' => 'Notes']);
        if (null == $contentType) {
            $contentType = new ContentType();
            $contentType->setName('Notes');
            $contentType->setStrategyClass(TextareaStrategy::class);
            $manager->persist($contentType);
        }
        $this->addReference(self::$NOTES, $contentType);

        $manager->flush();
    }
}
