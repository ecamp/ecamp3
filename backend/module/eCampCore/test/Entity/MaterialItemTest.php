<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\Period;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class MaterialItemTest extends AbstractTestCase {
    public function testMaterialItem() {
        $materialItem = new MaterialItem();
        $materialList = new MaterialList();

        $materialItem->setMaterialList($materialList);
        $materialItem->setArticle('article');
        $materialItem->setQuantity(2);
        $materialItem->setUnit('unit');
        $this->assertEquals($materialList, $materialItem->getMaterialList());
        $this->assertEquals('article', $materialItem->getArticle());
        $this->assertEquals(2, $materialItem->getQuantity());
        $this->assertEquals('unit', $materialItem->getUnit());

        $materialItem->setQuantity(null);
        $materialItem->setUnit(null);
        $this->assertEquals(null, $materialItem->getQuantity());
        $this->assertEquals(null, $materialItem->getUnit());
    }

    public function testMaterialItemTarget() {
        $materialItem = new MaterialItem();
        $period = new Period();
        $activityContent = new ActivityContent();

        $period->addMaterialItem($materialItem);
        $this->assertCount(1, $period->getMaterialItems());
        $this->assertEquals($period, $materialItem->getPeriod());
        $this->isNull($materialItem->getActivityContent());
        $period->removeMaterialItem($materialItem);

        $materialItem->setActivityContent($activityContent);
        $this->assertEquals($activityContent, $materialItem->getActivityContent());
        $this->isNull($materialItem->getPeriod());
        $materialItem->setActivityContent(null);

        $period->addMaterialItem($materialItem);
        $this->assertCount(1, $period->getMaterialItems());
        $this->assertEquals($period, $materialItem->getPeriod());
        $this->isNull($materialItem->getActivityContent());
        $period->removeMaterialItem($materialItem);
    }
}
