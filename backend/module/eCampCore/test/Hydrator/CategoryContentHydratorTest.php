<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContent;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\CategoryContentHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CategoryContentHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        $category = new Category();
        $contentType = new ContentType();
        $contentType->setName('ContentTypeName');
        $categoryContent = new CategoryContent();
        $categoryContent->setCategory($category);
        $categoryContent->setContentType($contentType);
        $categoryContent->setInstanceName('CategoryContentName');

        $hydrator = new CategoryContentHydrator();
        $data = $hydrator->extract($categoryContent);

        $this->assertEquals('CategoryContentName', $data['instanceName']);
        $this->assertEquals('ContentTypeName', $data['contentTypeName']);
    }

    public function testHydrate(): void {
        $categoryContent = new CategoryContent();
        $data = [
            'instanceName' => 'CategoryContentName',
        ];

        $hydrator = new CategoryContentHydrator();
        $hydrator->hydrate($data, $categoryContent);

        $this->assertEquals('CategoryContentName', $categoryContent->getInstanceName());
    }
}
