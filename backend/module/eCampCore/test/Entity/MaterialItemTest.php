<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
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
        $materialItem->setAmount(2);
        $materialItem->setUnit('unit');
        $this->assertEquals($materialList, $materialItem->getMaterialList());
        $this->assertEquals('article', $materialItem->getArticle());
        $this->assertEquals(2, $materialItem->getAmount());
        $this->assertEquals('unit', $materialItem->getUnit());
        
        $materialItem->setAmount(null);
        $materialItem->setUnit(null);
        $this->assertEquals(null, $materialItem->getAmount());
        $this->assertEquals(null, $materialItem->getUnit());
    }

    public function testMaterialItemTarget() {
        $materialItem = new MaterialItem();
        $period = new Period();
        $activityContent = new ActivityContent();

        $materialItem->setPeriod($period);
        $this->assertEquals($period, $materialItem->getPeriod());
        $this->isNull($materialItem->getActivityContent());

        $materialItem->setActivityContent($activityContent);
        $this->assertEquals($activityContent, $materialItem->getActivityContent());
        $this->isNull($materialItem->getPeriod());
        
        $materialItem->setPeriod($period);
        $this->assertEquals($period, $materialItem->getPeriod());
        $this->isNull($materialItem->getActivityContent());
    }
}
