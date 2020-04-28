<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Plugin;

class PluginData extends AbstractFixture {
    public static $TEXTAREA = Plugin::class.':TEXTAREA';
    public static $RICHTEXT = Plugin::class.':RICHTEXT';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Plugin::class);

        $plugin = $repository->findOneBy(['name' => 'TextArea']);
        if (null == $plugin) {
            $plugin = new Plugin();
            $plugin->setName('TextArea');
            $plugin->setActive(true);
            $plugin->setStrategyClass('');
            $manager->persist($plugin);
        }
        $this->addReference(self::$TEXTAREA, $plugin);

        $plugin = $repository->findOneBy(['name' => 'RichText']);
        if (null == $plugin) {
            $plugin = new Plugin();
            $plugin->setName('RichText');
            $plugin->setActive(true);
            $plugin->setStrategyClass('');
            $manager->persist($plugin);
        }
        $this->addReference(self::$RICHTEXT, $plugin);

        $manager->flush();
    }
}
