<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContentType;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryContentTypeTest extends AbstractTestCase {
    public function testCategoryContentType(): void {
        $camp = new Camp();

        $contentType = new ContentType();

        $category = new Category();
        $category->setCamp($camp);
        $category->setName('CategoryName');

        $categoryContentType = new CategoryContentType();
        $categoryContentType->setCategory($category);
        $categoryContentType->setContentType($contentType);

        $this->assertEquals($category, $categoryContentType->getCategory());
        $this->assertEquals($contentType, $categoryContentType->getContentType());
        $this->assertEquals($camp, $categoryContentType->getCamp());
    }
}
