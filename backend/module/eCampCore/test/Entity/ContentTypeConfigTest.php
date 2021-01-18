<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\ContentTypeConfig;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ContentTypeConfigTest extends AbstractTestCase {
    public function testContentTypeConfig() {
        $camp = new Camp();
        $activityCategory = new ActivityCategory();
        $contentType = new ContentType();
        $contentTypeConfig = new ContentTypeConfig();

        $camp->addActivityCategory($activityCategory);
        $activityCategory->addContentTypeConfig($contentTypeConfig);
        $contentTypeConfig->setContentType($contentType);
        $contentTypeConfig->setRequired(true);
        $contentTypeConfig->setMultiple(false);

        $this->assertEquals($camp, $contentTypeConfig->getCamp());
        $this->assertEquals($activityCategory, $contentTypeConfig->getActivityCategory());
        $this->assertEquals($contentType, $contentTypeConfig->getContentType());
        $this->assertTrue($contentTypeConfig->getRequired());
        $this->assertFalse($contentTypeConfig->getMultiple());
    }
}
