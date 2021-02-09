<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\CategoryContentTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\CategoryContentTemplateHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryContentTemplateHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $categoryTemplate = new CategoryTemplate();
        $contentType = new ContentType();
        $contentType->setName('ContentTypeName');
        $categoryContentTemplate = new CategoryContentTemplate();
        $categoryContentTemplate->setCategoryTemplate($categoryTemplate);
        $categoryContentTemplate->setContentType($contentType);
        $categoryContentTemplate->setInstanceName('CategoryContentName');

        $hydrator = new CategoryContentTemplateHydrator();
        $data = $hydrator->extract($categoryContentTemplate);

        $this->assertEquals('CategoryContentName', $data['instanceName']);
        $this->assertEquals('ContentTypeName', $data['contentTypeName']);
    }

    public function testHydrate() {
        $categoryContentTemplate = new CategoryContentTemplate();
        $data = [
            'instanceName' => 'CategoryContentName',
        ];

        $hydrator = new CategoryContentTemplateHydrator();
        $hydrator->hydrate($data, $categoryContentTemplate);

        $this->assertEquals('CategoryContentName', $categoryContentTemplate->getInstanceName());
    }
}
