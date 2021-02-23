<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\CategoryContentTemplate;
use eCamp\Core\Entity\CategoryContentTypeTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryTemplateTest extends AbstractTestCase {
    public function testCategoryTemplate(): void {
        $campTemplate = new CampTemplate();
        $categoryTemplate = new CategoryTemplate();
        $categoryTemplate->setName('ActivityType Name');
        $categoryTemplate->setColor('#FF00FF');
        $categoryTemplate->setNumberingStyle('i');
        $campTemplate->addCategoryTemplate($categoryTemplate);

        $this->assertEquals($campTemplate, $categoryTemplate->getCampTemplate());
        $this->assertEquals('ActivityType Name', $categoryTemplate->getName());
        $this->assertEquals('#FF00FF', $categoryTemplate->getColor());
        $this->assertEquals('i', $categoryTemplate->getNumberingStyle());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $categoryTemplate->getCategoryContentTemplates());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $categoryTemplate->getCategoryContentTypeTemplates());
    }

    public function testCategoryContentTypeTemplate(): void {
        $categoryTemplate = new CategoryTemplate();
        $categoryContentTypeTemplate = new CategoryContentTypeTemplate();

        $this->assertCount(0, $categoryTemplate->getCategoryContentTypeTemplates());
        $categoryTemplate->addCategoryContentTypeTemplate($categoryContentTypeTemplate);
        $this->assertContains($categoryContentTypeTemplate, $categoryTemplate->getCategoryContentTypeTemplates());
        $categoryTemplate->removeCategoryContentTypeTemplate($categoryContentTypeTemplate);
        $this->assertCount(0, $categoryTemplate->getCategoryContentTypeTemplates());
    }

    public function testCategoryContentTemplate(): void {
        $categoryTemplate = new CategoryTemplate();
        $categoryContentTemplate = new CategoryContentTemplate();

        $this->assertCount(0, $categoryTemplate->getCategoryContentTemplates());
        $categoryTemplate->addCategoryContentTemplate($categoryContentTemplate);
        $this->assertContains($categoryContentTemplate, $categoryTemplate->getCategoryContentTemplates());
        $categoryTemplate->removeCategoryContentTemplate($categoryContentTemplate);
        $this->assertCount(0, $categoryTemplate->getCategoryContentTemplates());
    }
}
