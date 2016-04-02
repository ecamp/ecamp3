<?php
namespace EcampDB\Fixtures\Prod;

use EcampCore\Entity\Plugin;
use EcampCore\Entity\EventTypePlugin;
use EcampCore\Entity\EventType;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class EventTypePluginFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const LAGERSPORT_STORYBOARD = 'eventtypeplugin-lagersport-storyboard';
    const LAGERSPORT_MATERIAL = 'eventtypeplugin-lagersport-material';

    const LAGERAKTIVITAET_STORYBOARD = 'eventtypeplugin-lageraktivitaet-storyboard';
    const LAGERAKTIVITAET_MATERIAL = 'eventtypeplugin-lageraktivitaet-material';

    const LAGERPROGRAMM_STORYBOARD = 'eventtypeplugin-lagerprogramm-storyboard';
    const LAGERPROGRAMM_MATERIAL = 'eventtypeplugin-lagerprogramm-material';

    public function load(ObjectManager $manager)
    {
        $this->init_($manager, array(
            array(
                'eventType' => EventTypeFixture::LAGERSPORT,
                'plugin' => PluginFixture::PLUGIN_STORYBOARD,
                'minNumberPluginInstances' => 0,
                'maxNumberPluginInstances' => 1,
                'reference' => self::LAGERSPORT_STORYBOARD
            ),
            array(
                'eventType' => EventTypeFixture::LAGERSPORT,
                'plugin' => PluginFixture::PLUGIN_MATERIAL,
                'minNumberPluginInstances' => 0,
                'maxNumberPluginInstances' => 1,
                'reference' => self::LAGERSPORT_MATERIAL
            ),

            array(
                'eventType' => EventTypeFixture::LAGERAKTIVITAET,
                'plugin' => PluginFixture::PLUGIN_STORYBOARD,
                'minNumberPluginInstances' => 0,
                'maxNumberPluginInstances' => 1,
                'reference' => self::LAGERAKTIVITAET_STORYBOARD
            ),
            array(
                'eventType' => EventTypeFixture::LAGERAKTIVITAET,
                'plugin' => PluginFixture::PLUGIN_MATERIAL,
                'minNumberPluginInstances' => 0,
                'maxNumberPluginInstances' => 1,
                'reference' => self::LAGERAKTIVITAET_MATERIAL
            ),

            array(
                'eventType' => EventTypeFixture::LAGERPROGRAMM,
                'plugin' => PluginFixture::PLUGIN_STORYBOARD,
                'minNumberPluginInstances' => 0,
                'maxNumberPluginInstances' => 1,
                'reference' => self::LAGERPROGRAMM_STORYBOARD
            ),
            array(
                'eventType' => EventTypeFixture::LAGERPROGRAMM,
                'plugin' => PluginFixture::PLUGIN_MATERIAL,
                'minNumberPluginInstances' => 0,
                'maxNumberPluginInstances' => 1,
                'reference' => self::LAGERPROGRAMM_MATERIAL
            )
        ));
    }

    private function init_(ObjectManager $manager, array $config)
    {
        $eventTypePluginRepo = $manager->getRepository('EcampCore\Entity\EventTypePlugin');

        foreach ($config as $eventTypeConfig) {
            /** @var EventType $eventType */
            $eventType = $this->getReference($eventTypeConfig['eventType']);
            /** @var Plugin $plugin */
            $plugin = $this->getReference($eventTypeConfig['plugin']);

            $minNumberPluginInstances = $eventTypeConfig['minNumberPluginInstances'];
            $maxNumberPluginInstances = $eventTypeConfig['maxNumberPluginInstances'];
            $reference = $eventTypeConfig['reference'];

            /** @var EventTypePlugin $eventTypePlugin */
            $eventTypePlugin = $eventTypePluginRepo->findOneBy(array(
                'eventType' => $eventType,
                'plugin' => $plugin
            ));

            if ($eventTypePlugin == null) {
                $eventTypePlugin = new EventTypePlugin($eventType, $plugin);
                $manager->persist($eventTypePlugin);
            }
            $eventTypePlugin->setMinNumberPluginInstances($minNumberPluginInstances);
            $eventTypePlugin->setMaxNumberPluginInstances($maxNumberPluginInstances);

            $this->addReference($reference, $eventTypePlugin);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 11;
    }
}
