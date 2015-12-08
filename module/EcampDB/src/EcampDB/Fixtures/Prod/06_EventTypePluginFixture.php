<?php
namespace EcampDB\Fixtures\Test;

use EcampCore\Entity\EventTemplateContainer;

use EcampCore\Entity\Plugin;

use EcampCore\Entity\EventTypePlugin;

use EcampCore\Entity\EventTemplate;

use EcampCore\Entity\Medium;

use EcampCore\Entity\EventType;

use EcampCore\Entity\CampType;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class EventTypePluginFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('EcampCore\Entity\EventTypePlugin');


        /** @var EventType $lagersport */
        $lagersport = $this->getReference(EventTypeFixture::LAGERSPORT);
        /** @var EventType $lageraktivitaet */
        $lageraktivitaet = $this->getReference(EventTypeFixture::LAGERAKTIVITAET);
        /** @var EventType $lagerprogramm */
        $lagerprogramm = $this->getReference(EventTypeFixture::LAGERPROGRAMM);


        /** @var Plugin $textarea */
        $textarea = $this->getReference(PluginFixture::PLUGIN_TEXTAREA);
        /** @var Plugin $material */
        $material = $this->getReference(PluginFixture::PLUGIN_MATERIAL);
        /** @var Plugin $storyboard */
        $storyboard = $this->getReference(PluginFixture::PLUGIN_STORYBOARD);



        if($repository->findOneBy(array('eventType' => $lagersport, 'plugin' => $storyboard)) == null){
            $eventTypePlugin = new EventTypePlugin($lagersport, $storyboard);
            $eventTypePlugin->setMinNumberPluginInstances(0);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $manager->persist($eventTypePlugin);
        }

        if($repository->findOneBy(array('eventType' => $lagersport, 'plugin' => $material)) == null){
            $eventTypePlugin = new EventTypePlugin($lagersport, $material);
            $eventTypePlugin->setMinNumberPluginInstances(0);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $manager->persist($eventTypePlugin);
        }



        if($repository->findOneBy(array('eventType' => $lageraktivitaet, 'plugin' => $storyboard)) == null){
            $eventTypePlugin = new EventTypePlugin($lageraktivitaet, $storyboard);
            $eventTypePlugin->setMinNumberPluginInstances(0);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $manager->persist($eventTypePlugin);
        }

        if($repository->findOneBy(array('eventType' => $lageraktivitaet, 'plugin' => $material)) == null){
            $eventTypePlugin = new EventTypePlugin($lageraktivitaet, $material);
            $eventTypePlugin->setMinNumberPluginInstances(0);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $manager->persist($eventTypePlugin);
        }



        if($repository->findOneBy(array('eventType' => $lagerprogramm, 'plugin' => $storyboard)) == null){
            $eventTypePlugin = new EventTypePlugin($lagerprogramm, $storyboard);
            $eventTypePlugin->setMinNumberPluginInstances(0);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $manager->persist($eventTypePlugin);
        }

        if($repository->findOneBy(array('eventType' => $lagerprogramm, 'plugin' => $material)) == null){
            $eventTypePlugin = new EventTypePlugin($lagerprogramm, $material);
            $eventTypePlugin->setMinNumberPluginInstances(0);
            $eventTypePlugin->setMinNumberPluginInstances(1);
            $manager->persist($eventTypePlugin);
        }

        $manager->flush();
    }


    public function getOrder()
    {
        return 6;
    }
}