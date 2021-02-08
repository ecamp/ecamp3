<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\MaterialListTemplate;

class MaterialListTemplateTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $MATERIALLISTTEMPLATE1 = MaterialListTemplate::class.':MATERIALLISTTEMPLATE1';

    public function load(ObjectManager $manager): void {
        /** @var CampTemplate $campTemplate */
        $campTemplate = $this->getReference(CampTemplateTestData::$TEMPLATE1);

        $materialListTemplate = new MaterialListTemplate();
        $materialListTemplate->setName('MaterialListTemplate1');
        $campTemplate->addMaterialListTemplate($materialListTemplate);

        $manager->persist($materialListTemplate);
        $manager->flush();

        $this->addReference(self::$MATERIALLISTTEMPLATE1, $materialListTemplate);
    }

    public function getDependencies() {
        return [CampTemplateTestData::class];
    }
}
