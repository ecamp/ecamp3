<?php

namespace EcampDB\Fixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use EcampCore\Entity\Plugin;

class PluginFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const PLUGIN_TEXTAREA = 'plugin-textarea';
    const PLUGIN_MATERIAL = 'plugin-material';
    const PLUGIN_STORYBOARD = 'plugin-storyboard';


    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('EcampCore\Entity\Plugin');

        $textarea = $repository->findOneBy(array('name' => 'textarea'));
        $material = $repository->findOneBy(array('name' => 'material'));
        $storyboard = $repository->findOneBy(array('name' => 'storyboard'));

        if($textarea == null){
            $textarea = new Plugin('textarea', 'EcampTextarea\Strategy');
            $manager->persist($textarea);
        }

        if($material == null){
            $material = new Plugin('material', 'EcampMaterial\Strategy');
            $manager->persist($material);
        }

        if($storyboard == null){
            $storyboard = new Plugin('storyboard', 'EcampStoryboard\Strategy');
            $manager->persist($storyboard);
        }


        $manager->flush();

        $this->addReference(self::PLUGIN_TEXTAREA, $textarea);
        $this->addReference(self::PLUGIN_MATERIAL, $material);
        $this->addReference(self::PLUGIN_STORYBOARD, $storyboard);
    }

    public function getOrder()
    {
        return 2;
    }

}