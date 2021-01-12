<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityTypeContentTypeTest extends AbstractTestCase {
    public function testActivityTypeContentType() {
        $activityType = new ActivityType();
        $contentType = new ContentType();

        $activityTypeContentType = new ActivityTypeContentType();
        $activityTypeContentType->setActivityType($activityType);
        $activityTypeContentType->setContentType($contentType);
        $activityTypeContentType->setDefaultInstances(1);

        $this->assertEquals($activityType, $activityTypeContentType->getActivityType());
        $this->assertEquals($contentType, $activityTypeContentType->getContentType());
        $this->assertEquals(1, $activityTypeContentType->getDefaultInstances());
    }
}
