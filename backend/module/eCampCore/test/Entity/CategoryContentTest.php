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

        $this->assertEquals($category, $categoryContent->getCategory());
        $this->assertEquals($contentType, $categoryContent->getContentType());
        $this->assertEquals('CategoryContentName', $categoryContent->getInstanceName());
        $this->assertEquals($camp, $categoryContent->getCamp());
    }
}
