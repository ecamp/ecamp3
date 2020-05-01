<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Plugin;
use eCamp\Plugin\Storyboard\Strategy as StoryboardStrategy;
use eCamp\Plugin\Textarea\Strategy as TextareaStrategy;

class PluginData extends AbstractFixture {
    public static $TEXTAREA = Plugin::class.':TEXTAREA';
    public static $RICHTEXT = Plugin::class.':RICHTEXT';
    public static $STORYBOARD = Plugin::class.':STORYBOARD';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Plugin::class);

        $plugin = $repository->findOneBy(['name' => 'TextArea']);
        if (null == $plugin) {
            $plugin = new Plugin();
            $plugin->setName('Textarea');
            $plugin->setActive(true);
            $plugin->setStrategyClass(TextareaStrategy::class);
            $manager->persist($plugin);
        }
        $this->addReference(self::$TEXTAREA, $plugin);

        $plugin = $repository->findOneBy(['name' => 'RichText']);
        if (null == $plugin) {
            $plugin = new Plugin();
            $plugin->setName('Richtext');
            $plugin->setActive(true);
            $plugin->setStrategyClass(TextareaStrategy::class);
            $manager->persist($plugin);
        }
        $this->addReference(self::$RICHTEXT, $plugin);

        $plugin = $repository->findOneBy(['name' => 'storyboard']);
        if (null == $plugin) {
            $plugin = new Plugin();
            $plugin->setName('Storyboard');
            $plugin->setActive(true);
            $plugin->setStrategyClass(StoryboardStrategy::class);
            $manager->persist($plugin);
        }
        $this->addReference(self::$STORYBOARD, $plugin);

        $manager->flush();
    }
}
