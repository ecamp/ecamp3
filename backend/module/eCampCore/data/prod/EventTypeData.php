<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Core\Entity\EventTypePlugin;

class EventTypeData extends AbstractFixture implements DependentFixtureInterface {
    public static $LAGERSPORT = 'LAGERSPORT';
    public static $LAGERAKTIVITAET = 'LAGERAKTIVITAET';

    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(EventType::class);

        $eventType = $repository->findOneBy(['name' => 'Lagersport']);
        if ($eventType == null) {
            $eventType = new EventType();
            $eventType->setName('Lagersport');
            $eventType->setDefaultColor('ff0000');
            $eventType->setDefaultNumberingStyle('1');
            $manager->persist($eventType);

            $plugin = $this->getReference(PluginData::$TEXTAREA);
            $eventTypePlugin = new EventTypePlugin();
            $eventTypePlugin->setPlugin($plugin);
            $eventTypePlugin->setMinNumberPluginInstances(0);
            $eventTypePlugin->setMaxNumberPluginInstances(3);
            $eventType->addEventTypePlugin($eventTypePlugin);
            $manager->persist($eventTypePlugin);

            $eventTypeFactory = new EventTypeFactory();
            $eventTypeFactory->setName('Wanderung');
            $eventTypeFactory->setFactoryName('');
            $eventType->addEventTypeFactory($eventTypeFactory);
            $manager->persist($eventTypeFactory);

            $eventTemplate = new EventTemplate();
            $eventTemplate->setMedium(EventTemplate::MEDIUM_WEB);
            $eventTemplate->setFilename('ls_web');
            $eventType->addEventTemplate($eventTemplate);
            $manager->persist($eventTemplate);

            $eventTemplateCont = new EventTemplateContainer();
            $eventTemplateCont->setContainerName('a');
            $eventTemplateCont->setFilename('a.html');
            $eventTemplateCont->setEventTypePlugin($eventTypePlugin);
            $eventTemplate->addEventTemplateContainer($eventTemplateCont);
            $manager->persist($eventTemplateCont);
        }


        $this->addReference(self::$LAGERSPORT, $eventType);


        $eventType = $repository->findOneBy(['name' => 'Lageraktivität']);
        if ($eventType == null) {
            $eventType = new EventType();
            $eventType->setName('Lageraktivität');
            $eventType->setDefaultColor('00ff00');
            $eventType->setDefaultNumberingStyle('A');
            $manager->persist($eventType);

            $plugin = $this->getReference(PluginData::$TEXTAREA);
            $eventTypePlugin = new EventTypePlugin();
            $eventTypePlugin->setPlugin($plugin);
            $eventTypePlugin->setMinNumberPluginInstances(0);
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
        return [ PluginData::class ];
    }
}
