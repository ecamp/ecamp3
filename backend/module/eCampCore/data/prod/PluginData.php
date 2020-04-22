<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Plugin;
use eCamp\Plugin\Textarea\Strategy as TextareaStrategy;
use eCamp\Plugin\Storyboard\Strategy as StoryboardStrategy;

class PluginData extends AbstractFixture {
    public static $TEXTAREA = Plugin::class . ':TEXTAREA';
    public static $RICHTEXT = Plugin::class . ':RICHTEXT';
    public static $STORYBOARD = Plugin::class . ':STORYBOARD';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Plugin::class);


        $plugin = $repository->findOneBy([ 'name' => 'TextArea' ]);
        if ($plugin == null) {
            $plugin = new Plugin();
            $plugin->setName('TextArea');
            $plugin->setActive(true);
            $plugin->setStrategyClass(TextareaStrategy::class);
            $manager->persist($plugin);
        }
        $this->addReference(self::$TEXTAREA, $plugin);

        $plugin = $repository->findOneBy([ 'name' => 'RichText' ]);
        if ($plugin == null) {
            $plugin = new Plugin();
            $plugin->setName('RichText');
            $plugin->setActive(true);
            $plugin->setStrategyClass(TextareaStrategy::class);
            $manager->persist($plugin);
        }
        $this->addReference(self::$RICHTEXT, $plugin);

        $plugin = $repository->findOneBy([ 'name' => 'storyboard' ]);
        if ($plugin == null) {
            $plugin = new Plugin();
            $plugin->setName('storyboard');
            $plugin->setActive(true);
            $plugin->setStrategyClass(StoryboardStrategy::class);
            $manager->persist($plugin);
        }
        $this->addReference(self::$STORYBOARD, $plugin);


        $manager->flush();
    }
}
