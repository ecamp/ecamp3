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
        $config_key = 'test';
        $config_value = 4;
        $config = [$config_key => $config_value];

        $contentType->setName('TestContentType');
        $contentType->setActive(true);
        $contentType->setStrategyClass('ContentTypeStrategyClass');
        $contentType->setJsonConfig($config);

        $this->assertEquals('TestContentType', $contentType->getName());
        $this->assertTrue($contentType->getActive());
        $this->assertEquals('ContentTypeStrategyClass', $contentType->getStrategyClass());
        $this->assertEquals($config, $contentType->getJsonConfig());
        $this->assertEquals($config_value, $contentType->getConfig($config_key));
    }
}
