<?php

namespace EcampDB\Fixtures\Prod;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use EcampCore\Entity\Plugin;

class PluginFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const PLUGIN_TEXTAREA = 'plugin-textarea';
    const PLUGIN_MATERIAL = 'plugin-material';
    const PLUGIN_STORYBOARD = 'plugin-storyboard';
    const PLUGIN_COURSE_AIM = 'plugin-course-aim';

    public function load(ObjectManager $manager)
    {
        $this->load_($manager, array(
            array(
                'name' => 'textarea',
                'strategy' => 'EcampTextarea\StrategyFactory',
                'reference' => self::PLUGIN_TEXTAREA
            ),
            array(
                'name' => 'material',
                'strategy' => 'EcampMaterial\StrategyFactory',
                'reference' => self::PLUGIN_MATERIAL
            ),
            array(
                'name' => 'storyboard',
                'strategy' => 'EcampStoryboard\StrategyFactory',
                'reference' => self::PLUGIN_STORYBOARD
            ),
            array(
                'name' => 'course-aim',
                'strategy' => 'EcampCourseAim\StrategyFactory',
                'reference' => self::PLUGIN_COURSE_AIM
            ),
        ));
    }

    private function load_(ObjectManager $manager, array $config)
    {
        $pluginRepo = $manager->getRepository('EcampCore\Entity\Plugin');

        foreach ($config as $pluginConfig) {
            $name = $pluginConfig['name'];
            $strategy = $pluginConfig['strategy'];
            $reference = $pluginConfig['reference'];

            /** @var Plugin $plugin */
            $plugin = $pluginRepo->findOneBy(array('name' => $name));

            if ($plugin == null) {
                $plugin = new Plugin($name, $strategy);
                $manager->persist($plugin);
            } else {
                $plugin->setStrategyClass($strategy);
            }

            $this->addReference($reference, $plugin);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}
