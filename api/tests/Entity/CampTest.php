<?php

namespace App\Tests\Entity;

use App\Entity\Camp;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\MaterialItem;
use App\Entity\MaterialList;
use App\Util\EntityMap;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CampTest extends TestCase {
    private Camp $campPrototype;
    private Category $categoryPrototype;
    private ColumnLayout $rootContentNodePrototype;
    private MaterialNode $materialContentNodePrototype;
    private MaterialList $materialListPrototype;
    private MaterialItem $materialItemPrototype;

    public function setUp(): void {
        $this->campPrototype = new Camp();
        $this->campPrototype->isPrototype = true;
        $this->campPrototype->name = 'camp-name';
        $this->campPrototype->title = 'camp-title';
        $this->campPrototype->motto = 'camp-motto';

        $this->categoryPrototype = new Category();
        $this->categoryPrototype->name = 'category-name';
        $this->categoryPrototype->color = '#FF00FF';
        $this->categoryPrototype->numberingStyle = 'a';
        $this->campPrototype->addCategory($this->categoryPrototype);

        $this->rootContentNodePrototype = new ColumnLayout();
        $this->categoryPrototype->setRootContentNode($this->rootContentNodePrototype);

        $this->materialContentNodePrototype = new MaterialNode();
        $this->rootContentNodePrototype->addChild($this->materialContentNodePrototype);

        $this->materialListPrototype = new MaterialList();
        $this->materialListPrototype->name = 'list-name';
        $this->campPrototype->addMaterialList($this->materialListPrototype);

        $this->materialItemPrototype = new MaterialItem();
        $this->materialItemPrototype->article = 'test-article';
        $this->materialItemPrototype->quantity = 1;
        $this->materialItemPrototype->unit = 'CU';
        $this->materialListPrototype->addMaterialItem($this->materialItemPrototype);
        $this->materialContentNodePrototype->addMaterialItem($this->materialItemPrototype);
    }

    public function testCopyFromPrototype() {
        $camp = new Camp();
        $camp->copyFromPrototype($this->campPrototype, new EntityMap());

        $this->assertEquals($this->campPrototype->getId(), $camp->campPrototypeId);

        $this->assertCount(1, $camp->categories);

        /** @var Category $category */
        $category = $camp->categories->first();
        $this->assertNotEquals($this->categoryPrototype, $category);
        $this->assertNotEquals($this->categoryPrototype->camp, $category->camp);
        $this->assertEquals($this->categoryPrototype->name, $category->name);
        $this->assertEquals($this->categoryPrototype->color, $category->color);
        $this->assertEquals($this->categoryPrototype->numberingStyle, $category->numberingStyle);

        /** @var ColumnLayout $rootContentNode */
        $rootContentNode = $category->rootContentNode;
        $this->assertNotEquals($this->rootContentNodePrototype, $rootContentNode);
        $this->assertEquals($this->rootContentNodePrototype->columns[0]['slot'], $rootContentNode->columns[0]['slot']);
        $this->assertEquals($this->rootContentNodePrototype->columns[0]['width'], $rootContentNode->columns[0]['width']);

        $this->assertCount(1, $rootContentNode->children);

        /** @var MaterialNode $materialContentNode */
        $materialContentNode = $rootContentNode->children->first();
        $this->assertNotEquals($this->materialContentNodePrototype, $materialContentNode);

        $this->assertCount(1, $camp->materialLists);

        /** @var MaterialList $materialList */
        $materialList = $camp->materialLists->first();
        $this->assertNotEquals($this->materialListPrototype, $materialList);
        $this->assertNotEquals($this->materialListPrototype->camp, $materialList->camp);
        $this->assertEquals($this->materialListPrototype->name, $materialList->name);

        $this->assertCount(1, $materialContentNode->materialItems);
        $this->assertCount(1, $materialList->materialItems);
        $this->assertEquals($materialList->materialItems->first(), $materialContentNode->materialItems->first());

        /** @var MaterialItem $materialItem */
        $materialItem = $materialList->materialItems->first();
        $this->assertNotEquals($this->materialItemPrototype, $materialItem);
        $this->assertEquals($this->materialItemPrototype->article, $materialItem->article);
        $this->assertEquals($this->materialItemPrototype->quantity, $materialItem->quantity);
        $this->assertEquals($this->materialItemPrototype->unit, $materialItem->unit);
    }
}
