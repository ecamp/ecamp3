<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;

class MaterialItemTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $MATERIALITEM1 = MaterialItem::class.':MATERIALITEM1';

    public function load(ObjectManager $manager) {
        /** @var MaterialList $materialList */
        $materialList = $this->getReference(MaterialListTestData::$MATERIALLIST1);

        $materialItem = new MaterialItem();
        $materialItem->setQuantity(2);
        $materialItem->setUnit('kg');
        $materialItem->setArticle('art');
        $materialList->addMaterialItem($materialItem);

        $manager->persist($materialItem);
        $manager->flush();

        $this->addReference(self::$MATERIALITEM1, $materialItem);
    }

    public function getDependencies() {
        return [MaterialListTestData::class];
    }
}
