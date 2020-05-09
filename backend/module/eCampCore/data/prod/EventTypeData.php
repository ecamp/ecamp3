<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Core\Entity\EventTypePlugin;

class EventTypeData extends AbstractFixture implements DependentFixtureInterface {
    public static $LAGERSPORT = 'LAGERSPORT';
    public static $LAGERAKTIVITAET = 'LAGERAKTIVITAET';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(EventType::class);

        $eventType = $repository->findOneBy(['name' => 'Lagersport']);
        if (null == $eventType) {
            $eventType = new EventType();
            $eventType->setName('Lagersport');
            $eventType->setDefaultColor('#4CAF50');
            $eventType->setDefaultNumberingStyle('1');
            $manager->persist($eventType);

            $plugin = $this->getReference(PluginData::$TEXTAREA);
            $eventTypePlugin = new EventTypePlugin();
            $eventTypePlugin->setPlugin($plugin);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $eventTypePlugin->setMaxNumberPluginInstances(3);
            $eventType->addEventTypePlugin($eventTypePlugin);
            $manager->persist($eventTypePlugin);

            $plugin = $this->getReference(PluginData::$STORYBOARD);
            $eventTypePlugin = new EventTypePlugin();
            $eventTypePlugin->setPlugin($plugin);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $eventTypePlugin->setMaxNumberPluginInstances(3);
            $eventType->addEventTypePlugin($eventTypePlugin);
            $manager->persist($eventTypePlugin);

            $eventTypeFactory = new EventTypeFactory();
            $eventTypeFactory->setName('Wanderung');
            $eventTypeFactory->setFactoryName('');
            $eventType->addEventTypeFactory($eventTypeFactory);
            $manager->persist($eventTypeFactory);
        }

        $this->addReference(self::$LAGERSPORT, $eventType);

        $eventType = $repository->findOneBy(['name' => 'Lageraktivität']);
        if (null == $eventType) {
            $eventType = new EventType();
            $eventType->setName('Lageraktivität');
            $eventType->setDefaultColor('#FF9800');
            $eventType->setDefaultNumberingStyle('A');
            $manager->persist($eventType);

            $plugin = $this->getReference(PluginData::$TEXTAREA);
            $eventTypePlugin = new EventTypePlugin();
            $eventTypePlugin->setPlugin($plugin);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $eventTypePlugin->setMaxNumberPluginInstances(100);
            $eventType->addEventTypePlugin($eventTypePlugin);
            $manager->persist($eventTypePlugin);

            $plugin = $this->getReference(PluginData::$RICHTEXT);
            $eventTypePlugin = new EventTypePlugin();
            $eventTypePlugin->setPlugin($plugin);
            $eventTypePlugin->setMinNumberPluginInstances(3);
            $eventTypePlugin->setMaxNumberPluginInstances(3);
            $eventType->addEventTypePlugin($eventTypePlugin);
            $manager->persist($eventTypePlugin);

            $eventTypeFactory = new EventTypeFactory();
            $eventTypeFactory->setName('TABS');
            $eventTypeFactory->setFactoryName('');
            $eventType->addEventTypeFactory($eventTypeFactory);
            $manager->persist($eventTypeFactory);
        }
        $this->addReference(self::$LAGERAKTIVITAET, $eventType);

        $manager->flush();
    }

    public function getDependencies() {
        return [PluginData::class];
    }
}
