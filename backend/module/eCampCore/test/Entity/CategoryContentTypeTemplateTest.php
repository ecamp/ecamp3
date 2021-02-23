<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\CategoryContentTypeTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryContentTypeTemplateTest extends AbstractTestCase {
    public function testCategoryContentTypeTemplate(): void {
        $campTemplate = new CampTemplate();

        $contentType = new ContentType();

        $categoryTemplate = new CategoryTemplate();
        $categoryTemplate->setCampTemplate($campTemplate);
        $categoryTemplate->setName('CategoryName');

        $categoryContentTypeTemplate = new CategoryContentTypeTemplate();
        $categoryContentTypeTemplate->setCategoryTemplate($categoryTemplate);
        $categoryContentTypeTemplate->setContentType($contentType);

        $this->assertEquals($categoryTemplate, $categoryContentTypeTemplate->getCategoryTemplate());
        $this->assertEquals($contentType, $categoryContentTypeTemplate->getContentType());
    }
}
