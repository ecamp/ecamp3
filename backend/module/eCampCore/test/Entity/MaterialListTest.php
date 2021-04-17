<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class MaterialListTest extends AbstractTestCase {
    public function testMaterialList(): void {
        $materialList = new MaterialList();
        $camp = new Camp();

        $materialList->setName('name');
        $materialList->setCamp($camp);

        $this->assertEquals('name', $materialList->getName());
        $this->assertEquals($camp, $materialList->getCamp());
    }

    public function testMaterialListAddRemoveItem(): void {
        $materialList = new MaterialList();
        $materialItem = new MaterialItem();

        $this->assertCount(0, $materialList->getMaterialItems());

        $materialList->addMaterialItem($materialItem);

        $this->assertCount(1, $materialList->getMaterialItems());
        $this->assertEquals($materialList, $materialItem->getMaterialList());

        $materialList->removeMaterialItem($materialItem);

        $this->assertCount(0, $materialList->getMaterialItems());
        $this->isNull($materialItem->getMaterialList());
    }
}
