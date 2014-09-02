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

class EventTypes extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $medium = new Medium(Medium::MEDIUM_WEB, true);
        $manager->persist($medium);

        $plugin_storyboard = new Plugin("Storyboard", "EcampStoryboard\StrategyFactory");
        $manager->persist($plugin_storyboard);

        $plugin_material = new Plugin("Material",  "EcampMaterial\StrategyFactory");
        $manager->persist($plugin_material);

        $type = new EventType();
        $type->setName("Lagersport");
        $type->setDefaultColor('red');
        $type->setDefaultNumberingStyle("1");

        $type->getCampTypes()->add($this->getReference('camptype-jugendsport'));
        $this->getReference('camptype-jugendsport')->getEventTypes()->add($type);

        $manager->persist($type);
        $this->addReference('eventtype-lagersport', $type);

        $template = new EventTemplate($type, $medium, "ecamp-web/event-templates/ausbildung/index");
        $manager->persist($template);

        $storyboard = new EventTypePlugin($type, $plugin_storyboard);
        $manager->persist($storyboard);

        $material = new EventTypePlugin($type, $plugin_material);
        $manager->persist($material);

        $container = new EventTemplateContainer($template, $storyboard, "Storyboard", "ecamp-web/event-templates/containers/tabs");
        $manager->persist($container);

        $container = new EventTemplateContainer($template, $material, "Material", "ecamp-web/event-templates/containers/linear");
        $manager->persist($container);

        /*
        $type = new EventType();
        $type->setName("Lageraktivität");
        $type->setDefaultColor('red');
        $type->setDefaultNumberingStyle("1");
        $manager->persist($type);
        */

        $manager->flush();
    }

    public function getOrder()
    {
        return 11;
    }
}
