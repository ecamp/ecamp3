<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampTemplateTest extends AbstractTestCase {
    public function testCampTemplate() {
        $campTemplate = new CampTemplate();
        $campTemplate->setName('CampTemplate.Name');

        $this->assertEquals('CampTemplate.Name', $campTemplate->getName());
    }

    public function testActivityCategoryTemplate() {
        $campTemplate = new CampTemplate();
        $categoryTemplate = new CategoryTemplate();

        $this->assertCount(0, $campTemplate->getCategoryTemplates());
        $campTemplate->addCategoryTemplate($categoryTemplate);
        $this->assertContains($categoryTemplate, $campTemplate->getCategoryTemplates());
        $campTemplate->removeCategoryTemplate($categoryTemplate);
        $this->assertCount(0, $campTemplate->getCategoryTemplates());
    }
}
