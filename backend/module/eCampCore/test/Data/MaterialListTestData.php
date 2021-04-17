<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\MaterialList;

class MaterialListTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $MATERIALLIST1 = MaterialList::class.':MATERIALLIST1';

    public function load(ObjectManager $manager): void {
        /** @var Camp $ccamp */
        $camp = $this->getReference(CampTestData::$CAMP1);

        $materialList = new MaterialList();
        $materialList->setName('MaterialList1');
        $camp->addMaterialList($materialList);

        $manager->persist($materialList);
        $manager->flush();

        $this->addReference(self::$MATERIALLIST1, $materialList);
    }

    public function getDependencies() {
        return [CampTestData::class];
    }
}
