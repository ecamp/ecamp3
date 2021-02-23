<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContent;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryContentTest extends AbstractTestCase {
    public function testCategoryContent() {
        $camp = new Camp();

        $contentType = new ContentType();

        $category = new Category();
        $category->setCamp($camp);
        $category->setName('CategoryName');

        $categoryContent = new CategoryContent();
        $categoryContent->setCategory($category);
        $categoryContent->setContentType($contentType);
        $categoryContent->setInstanceName('CategoryContentName');
        $categoryContent->setPosition('position');

        $this->assertEquals($category, $categoryContent->getCategory());
        $this->assertEquals($contentType, $categoryContent->getContentType());
        $this->assertEquals('CategoryContentName', $categoryContent->getInstanceName());
        $this->assertEquals('position', $categoryContent->getPosition());
        $this->assertEquals($camp, $categoryContent->getCamp());
    }

    public function testCategoryContentHierarchy() {
        $categoryContent = new CategoryContent();
        $childCategoryContent = new CategoryContent();

        // Add Child-CategoryContent
        $categoryContent->addChild($childCategoryContent);
        $this->assertCount(1, $categoryContent->getChildren());
        $this->assertEquals($categoryContent, $childCategoryContent->getParent());
        $this->assertTrue($categoryContent->isRoot());
        $this->assertFalse($childCategoryContent->isRoot());

        // Remove Child-CategoryContent
        $categoryContent->removeChild($childCategoryContent);
        $this->assertCount(0, $categoryContent->getChildren());
        $this->assertNull($childCategoryContent->getParent());
        $this->assertTrue($categoryContent->isRoot());
        $this->assertTrue($childCategoryContent->isRoot());
    }
}
