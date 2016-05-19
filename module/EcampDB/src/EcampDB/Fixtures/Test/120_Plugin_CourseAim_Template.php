<?php
namespace EcampDB\Fixtures\Test;


use EcampCourseAim\Entity\Template;
use EcampCourseAim\Entity\TemplateItem;

use EcampDB\Fixtures\Prod\CampTypeFixture;

use EcampCore\Entity\CampType;


use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use EcampDB\Fixtures\Prod\GroupFixture;

class PluginCourseAimTemplate extends AbstractFixture implements OrderedFixtureInterface
{
    const WOLF_BASISKURS = 'aim-template-wolf-basiskurs';

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->load_($manager, array(
            array(
                'name' => 'Wolfstufe Basiskurs Zielliste 2016',
                'revision' => 1,
                'campType' => CampTypeFixture::AUSBILDUNG_WOLF_BASIS,

                'items' => array(
                    array(
                        'text' => 'Der Kurs vermittelt den TN die Pfadigrundlagen.',
                        'children' => array(
                            array(
                                'text' => '... wissen, was die Pfadigrundlagen sind und erkennen deren Bezug zu ihrem Pfadialltag.'
                            ),
                            array(
                                'text' => '... kennen das Stufenmodell der Pfadibewegung und können die Wolfsstufe von der Biber- und der Pfadistufe abgrenzen'
                            )
                        )
                    )
                )
            ),
        ));
    }

    private function load_(ObjectManager $manager, array $config)
    {
        $repo = $manager->getRepository('EcampCourseAim\Entity\Template');

        foreach ($config as $templateConfig) {
            $name = $templateConfig['name'];
            $revision = $templateConfig['revision'];

            /** @var CampType $campType */
            $campType = $this->getReference($templateConfig['campType']);

            $template = $repo->findOneBy(array('name' => $name));

            if ($template == null) {
                $template = new Template($campType);
                $template->setName($name);
                $manager->persist($template);
            }

            $template->setRevision($revision);


            foreach ($templateConfig['items'] as $itemConfig) {
                $this->loadItem($itemConfig, null, $template);
            }

        }

        $manager->flush();
    }

    private function loadItem($itemConfig, $parent, $template){
        $text = $itemConfig['text'];

        $itemRepo = $this->manager->getRepository('EcampCourseAim\Entity\TemplateItem');
        $item = $itemRepo->findOneBy(array(
            'list' => $template,
            'text' => $text
        ));

        if( $item == null ) {
            $item = new TemplateItem($template, $parent);
            $item->setText($text);

            $this->manager->persist($item);
        }

        $children = $itemConfig['children'];
        if( $children ) {
            foreach ($children as $childConfig) {
                $this->loadItem($childConfig, $item, $template);
            }
        }
    }

    public function getOrder()
    {
        return 120;
    }
}
