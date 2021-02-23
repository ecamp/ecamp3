<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContent;
use eCamp\Core\Entity\CategoryContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryTest extends AbstractTestCase {
    public function testCategory(): void {
        $camp = new Camp();

        $category = new Category();
        $category->setCamp($camp);
        $category->setName('TestCategory');
        $category->setShort('TC');
        $category->setColor('#1fa2df');
        $category->setNumberingStyle('i');

        $this->assertEquals($camp, $category->getCamp());
        $this->assertEquals('TestCategory', $category->getName());
        $this->assertEquals('TC', $category->getShort());
        $this->assertEquals('#1fa2df', $category->getColor());
        $category->setColor('#FF00FF');
        $this->assertEquals('#FF00FF', $category->getColor());
        $this->assertEquals('i', $category->getNumberingStyle());
    }

    public function testCategoryContentType(): void {
        $category = new Category();
        $categoryContentType = new CategoryContentType();

        $this->assertCount(0, $category->getCategoryContentTypes());
        $category->addCategoryContentType($categoryContentType);
        $this->assertContains($categoryContentType, $category->getCategoryContentTypes());
        $category->removeCategoryContentType($categoryContentType);
        $this->assertCount(0, $category->getCategoryContentTypes());
    }

    public function testCategoryContent(): void {
        $category = new Category();
        $categoryContent = new CategoryContent();

        $this->assertCount(0, $category->getCategoryContents());
        $category->addCategoryContent($categoryContent);
        $this->assertContains($categoryContent, $category->getCategoryContents());
        $category->removeCategoryContent($categoryContent);
        $this->assertCount(0, $category->getCategoryContents());
    }

    public function testNumberingStyle(): void {
        $category = new Category();

        $this->assertEquals('31', $category->getStyledNumber(31));

        $category->setNumberingStyle('a');
        $this->assertEquals('ae', $category->getStyledNumber(31));

        $category->setNumberingStyle('A');
        $this->assertEquals('AE', $category->getStyledNumber(31));

        $category->setNumberingStyle('i');
        $this->assertEquals('xxxi', $category->getStyledNumber(31));

        $category->setNumberingStyle('I');
        $this->assertEquals('XXXI', $category->getStyledNumber(31));
    }
}
