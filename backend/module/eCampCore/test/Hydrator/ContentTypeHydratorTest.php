<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\ContentTypeHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ContentTypeHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $contentType = new ContentType();
        $contentType->setName('name');
        $contentType->setActive(true);
        $contentType->setStrategyClass('TestClass');

        $hydrator = new ContentTypeHydrator();
        $data = $hydrator->extract($contentType);

        $this->assertEquals('name', $data['name']);
        $this->assertTrue($data['active']);
    }

    public function testHydrate() {
        $contentType = new ContentType();
        $data = [
            'name' => 'name',
            'active' => true,
        ];

        $hydrator = new ContentTypeHydrator();
        $hydrator->hydrate($data, $contentType);

        $this->assertEquals('name', $contentType->getName());
        $this->assertTrue($contentType->getActive());
    }
}
