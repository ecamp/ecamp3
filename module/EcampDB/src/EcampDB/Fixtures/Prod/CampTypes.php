<?php
namespace EcampDB\Fixtures\Test;

use EcampCore\Entity\CampType;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use EcampCore\Entity\EventTemplate;
use EcampCore\Entity\EventTemplateContainer;
use EcampCore\Entity\EventType;
use EcampCore\Entity\EventTypePlugin;
use EcampCore\Entity\Medium;
use EcampCore\Entity\Plugin;

class CampTypes extends AbstractFixture implements OrderedFixtureInterface
{
    const MEDIUM_WEB = 'web';
    const MEDIUM_PRINT = 'print';

    const PLUGIN_MATERIAL = 'material';
    const PLUGIN_STORYBOARD = 'storyboard';
    const PLUGIN_TEXTAREA = 'textarea';

    const CAMP_TYPE_KINDERSPORT = 'kindersport';
    const CAMP_TYPE_JUGENDSPORT = 'jugendsport';
    const CAMP_TYPE_AUSBILDUNG = 'ausbildung';

    const EVENT_TYPE_LAGERSPORT = 'lagersport';
    const EVENT_TYPE_LAGERAKTIVITAET = 'lageraktivitaet';
    const EVENT_TYPE_LAGERPROGRAMM = 'lagerprogramm';

    public function load(ObjectManager $manager)
    {
        $this->createMedia($manager);
        $this->createPlugins($manager);

        $this->createEventTypes($manager);
        $this->createEventTypePlugins($manager);
        $this->createEventTemplates($manager);

        $this->createCampTypes($manager);
    }

    private function createMedia(ObjectManager $manager)
    {
        if ($this->getMedium($manager, self::MEDIUM_WEB) == null) {
            $this->createMedium($manager, self::MEDIUM_WEB, true);
        }

        if ($this->getMedium($manager, self::MEDIUM_PRINT) == null) {
            $this->createMedium($manager, self::MEDIUM_PRINT, false);
        }

        $manager->flush();
    }

    private function getMedium(ObjectManager $manager, $name)
    {
        return $manager->getRepository('EcampCore\Entity\Medium')->findOneBy(array('name' => $name));
    }

    private function createMedium(ObjectManager $manager, $name, $default)
    {
        $medium = new Medium($name, $default);
        $manager->persist($medium);

        return $medium;
    }

    private function createPlugins(ObjectManager $manager)
    {
        if ($this->getPlugin($manager, self::PLUGIN_MATERIAL) == null) {
            $this->createPlugin($manager, self::PLUGIN_MATERIAL, 'EcampMaterial\StrategyFactory');
        }

        if ($this->getPlugin($manager, self::PLUGIN_STORYBOARD) == null) {
            $this->createPlugin($manager, self::PLUGIN_STORYBOARD, 'EcampStoryboard\StrategyFactory');
        }

        if ($this->getPlugin($manager, self::PLUGIN_TEXTAREA) == null) {
            $this->createPlugin($manager, self::PLUGIN_TEXTAREA, 'EcampTextarea\StrategyFactory');
        }

        $manager->flush();
    }

    private function getPlugin(ObjectManager $manager, $name)
    {
        return $manager->getRepository('EcampCore\Entity\Plugin')->findOneBy(array('name' => $name));
    }

    private function createPlugin(ObjectManager $manager, $name, $strategyClass)
    {
        $plugin = new Plugin($name, $strategyClass);
        $manager->persist($plugin);

        return $plugin;
    }

    private function createEventTypes(ObjectManager $manager)
    {
        if ($this->getEventType($manager, self::EVENT_TYPE_LAGERSPORT) == null) {
            $this->createEventType($manager, self::EVENT_TYPE_LAGERSPORT, "Lagersport", "#ff5555", "a");
        }

        if ($this->getEventType($manager, self::EVENT_TYPE_LAGERAKTIVITAET) == null) {
            $this->createEventType($manager, self::EVENT_TYPE_LAGERAKTIVITAET, "Lageraktivität", "#55ff55", "1");
        }

        if ($this->getEventType($manager, self::EVENT_TYPE_LAGERPROGRAMM) == null) {
            $this->createEventType($manager, self::EVENT_TYPE_LAGERPROGRAMM, "Lagerprogramm", "#00ffff", "i");
        }

        $manager->flush();
    }

    /** @return EventType */
    private function getEventType(ObjectManager $manager, $type)
    {
        return $manager->getRepository('EcampCore\Entity\EventType')->findOneBy(array('type' => $type));
    }

    private function createEventType(ObjectManager $manager, $type, $name, $color, $numStyle)
    {
        $eventType = new EventType();
        $eventType->setName($name);
        $eventType->setType($type);
        $eventType->setDefaultColor($color);
        $eventType->setDefaultNumberingStyle($numStyle);
        $manager->persist($eventType);

        return $eventType;
    }

    private function createEventTypePlugins(ObjectManager $manager)
    {
        if ($this->getEventTypePlugin($manager, self::EVENT_TYPE_LAGERSPORT, self::PLUGIN_STORYBOARD) == null) {
            $this->createEventTypePlugin($manager, self::EVENT_TYPE_LAGERSPORT, self::PLUGIN_STORYBOARD);
        }

        if ($this->getEventTypePlugin($manager, self::EVENT_TYPE_LAGERAKTIVITAET, self::PLUGIN_STORYBOARD) == null) {
            $this->createEventTypePlugin($manager, self::EVENT_TYPE_LAGERAKTIVITAET, self::PLUGIN_STORYBOARD);
        }

        if ($this->getEventTypePlugin($manager, self::EVENT_TYPE_LAGERPROGRAMM, self::PLUGIN_STORYBOARD) == null) {
            $this->createEventTypePlugin($manager, self::EVENT_TYPE_LAGERPROGRAMM, self::PLUGIN_STORYBOARD);
        }

        $manager->flush();
    }

    private function getEventTypePlugin(ObjectManager $manager, $type, $plugin)
    {
        $eventType = $this->getEventType($manager, $type);
        $plugin = $this->getPlugin($manager, $plugin);

        return $manager->getRepository('EcampCore\Entity\EventTypePlugin')->findOneBy(array(
            'eventType' => $eventType, 'plugin' => $plugin
        ));
    }

    private function createEventTypePlugin(ObjectManager $manager, $type, $plugin)
    {
        $eventType = $this->getEventType($manager, $type);
        $plugin = $this->getPlugin($manager, $plugin);
        $eventTypePlugin = new EventTypePlugin($eventType, $plugin);

        $manager->persist($eventTypePlugin);

        return $eventTypePlugin;
    }

    private function createEventTemplates(ObjectManager $manager)
    {
        if ($this->getEventTemplate($manager, self::EVENT_TYPE_LAGERSPORT, self::MEDIUM_WEB) == null) {
            $eventTemplate = $this->createEventTemplate(
                $manager, self::EVENT_TYPE_LAGERSPORT, self::MEDIUM_WEB, 'ecamp-web/event-templates/lagersport/index');

            $this->createEventTemplateContainer(
                $manager, $eventTemplate, self::PLUGIN_STORYBOARD, 'Storyboard', 'ecamp-web/event-templates/containers/linear');
        }

        if ($this->getEventTemplate($manager, self::EVENT_TYPE_LAGERSPORT, self::MEDIUM_PRINT) == null) {
            $eventTemplate = $this->createEventTemplate(
                $manager, self::EVENT_TYPE_LAGERSPORT, self::MEDIUM_PRINT, 'ecamp-web/event-templates/lagersport/print.twig');

            $this->createEventTemplateContainer(
                $manager, $eventTemplate, self::PLUGIN_STORYBOARD, 'Storyboard', 'ecamp-web/event-templates/containers/linear');
        }

        $manager->flush();
    }

    private function getEventTemplate(ObjectManager $manager, $type, $medium)
    {
        $type = $this->getEventType($manager, $type);
        $medium = $this->getMedium($manager, $medium);

        return $manager->getRepository('EcampCore\Entity\EventTemplate')->findOneBy(array(
            'eventType' => $type, 'medium' => $medium
        ));
    }

    private function createEventTemplate(ObjectManager $manager, $type, $medium, $filename)
    {
        $type = $this->getEventType($manager, $type);

        var_dump($manager->getRepository('EcampCore\Entity\Medium')->findAll());
        $medium = $this->getMedium($manager, $medium);
        var_dump($medium);

        $eventTemplate = new EventTemplate($type, $medium, $filename);
        $manager->persist($eventTemplate);

        return $eventTemplate;
    }

    private function createEventTemplateContainer(ObjectManager $manager, EventTemplate $eventTemplate, $plugin, $name, $filename)
    {
        $type = $eventTemplate->getEventType()->getType();
        $eventTypePlugin = $this->getEventTypePlugin($manager, $type, $plugin);

        $eventTemplateContainer = new EventTemplateContainer($eventTemplate, $eventTypePlugin, $name, $filename);
        $manager->persist($eventTemplateContainer);

        return $eventTemplateContainer;
    }

    private function createCampTypes(ObjectManager $manager)
    {
        if ($this->getCampType($manager, self::CAMP_TYPE_KINDERSPORT) == null) {
            $campType = $this->createCampType($manager, self::CAMP_TYPE_KINDERSPORT, "J+S Kindersport");
            $this->getEventType($manager, self::EVENT_TYPE_LAGERSPORT)->getCampTypes()->add($campType);
            $this->getEventType($manager, self::EVENT_TYPE_LAGERAKTIVITAET)->getCampTypes()->add($campType);
            $this->getEventType($manager, self::EVENT_TYPE_LAGERPROGRAMM)->getCampTypes()->add($campType);
        }

        if ($this->getCampType($manager, self::CAMP_TYPE_JUGENDSPORT) == null) {
            $campType = $this->createCampType($manager, self::CAMP_TYPE_JUGENDSPORT, "J+S Jugendsport");
            $this->getEventType($manager, self::EVENT_TYPE_LAGERSPORT)->getCampTypes()->add($campType);
            $this->getEventType($manager, self::EVENT_TYPE_LAGERAKTIVITAET)->getCampTypes()->add($campType);
            $this->getEventType($manager, self::EVENT_TYPE_LAGERPROGRAMM)->getCampTypes()->add($campType);
        }

        if ($this->getCampType($manager, self::CAMP_TYPE_AUSBILDUNG) == null) {
            $this->createCampType($manager, self::CAMP_TYPE_AUSBILDUNG, "J+S Ausbildung");
        }

        $manager->flush();
    }

    private function getCampType(ObjectManager $manager, $type)
    {
        return $manager->getRepository('EcampCore\Entity\CampType')->findOneBy(array('type' => $type));
    }

    private function createCampType(ObjectManager $manager, $type, $name)
    {
        $campType = new CampType($name, $type);
        $manager->persist($campType);

        return $campType;
    }

    public function getOrder()
    {
        return 10;
    }
}
