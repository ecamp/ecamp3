<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\MaterialList;

class MaterialListPrototypeTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $PROTOTYPE1 = MaterialList::class.':Prototype1';

    public function load(ObjectManager $manager): void {
        /** @var Camp $camp */
        $camp = $this->getReference(CampPrototypeTestData::$PROTOTYPE1);

        $materialList = new MaterialList();
        $materialList->setName('MaterialListPrototype1');
        $camp->addMaterialList($materialList);

        $manager->persist($materialList);
        $manager->flush();

        $this->addReference(self::$PROTOTYPE1, $materialList);
    }

    public function getDependencies() {
        return [CampPrototypeTestData::class];
    }
}
