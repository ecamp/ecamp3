<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\CategoryContentTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryContentTemplateTest extends AbstractTestCase {
    public function testCategoryContentTemplate() {
        $campTemplate = new CampTemplate();

        $contentType = new ContentType();

        $categoryTemplate = new CategoryTemplate();
        $categoryTemplate->setCampTemplate($campTemplate);
        $categoryTemplate->setName('CategoryName');

        $categoryContentTemplate = new CategoryContentTemplate();
        $categoryContentTemplate->setCategoryTemplate($categoryTemplate);
        $categoryContentTemplate->setContentType($contentType);
        $categoryContentTemplate->setInstanceName('CategoryContentName');

        $this->assertEquals($categoryTemplate, $categoryContentTemplate->getCategoryTemplate());
        $this->assertEquals($contentType, $categoryContentTemplate->getContentType());
        $this->assertEquals('CategoryContentName', $categoryContentTemplate->getInstanceName());
    }
}
