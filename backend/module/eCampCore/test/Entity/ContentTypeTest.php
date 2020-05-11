<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ContentTypeTest extends AbstractTestCase {
    public function testContentType() {
        $contentType = new ContentType();
        $contentType->setName('TestContentType');
        $contentType->setActive(true);
        $contentType->setStrategyClass('ContentTypeStrategyClass');

        $this->assertEquals('TestContentType', $contentType->getName());
        $this->assertTrue($contentType->getActive());
        $this->assertEquals('ContentTypeStrategyClass', $contentType->getStrategyClass());
    }
}
