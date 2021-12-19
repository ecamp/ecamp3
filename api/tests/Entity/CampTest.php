<?php

namespace App\Tests\Entity;

use App\Entity\Camp;
use App\Entity\Category;
use App\Entity\MaterialList;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CampTest extends TestCase {
    private Camp $campPrototype;
    private Category $categoryPrototype;
    private MaterialList $materialListPrototype;

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

        $this->materialListPrototype = new MaterialList();
        $this->materialListPrototype->name = 'list-name';
        $this->campPrototype->addMaterialList($this->materialListPrototype);
    }

    public function testCopyFromPrototype() {
        $camp = new Camp();
        $camp->copyFromPrototype($this->campPrototype);

        $this->assertEquals($this->campPrototype->getId(), $camp->campPrototypeId);

        $this->assertCount(1, $camp->categories);
        $category = $camp->categories->first();
        $this->assertNotEquals($this->categoryPrototype, $category);
        $this->assertNotEquals($this->categoryPrototype->camp, $category->camp);
        $this->assertEquals($this->categoryPrototype->name, $category->name);
        $this->assertEquals($this->categoryPrototype->color, $category->color);
        $this->assertEquals($this->categoryPrototype->numberingStyle, $category->numberingStyle);

        $this->assertCount(1, $camp->materialLists);
        $materialList = $camp->materialLists->first();
        $this->assertNotEquals($this->materialListPrototype, $materialList);
        $this->assertNotEquals($this->materialListPrototype->camp, $materialList->camp);
        $this->assertEquals($this->materialListPrototype->name, $materialList->name);
    }
}
