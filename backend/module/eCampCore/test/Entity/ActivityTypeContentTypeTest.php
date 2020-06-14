<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Laminas\Json\Json;

/**
 * @internal
 */
class ActivityTypeContentTypeTest extends AbstractTestCase {
    public function testActivityTypeContentType() {
        $activityType = new ActivityType();
        $contentType = new ContentType();
        $config = Json::encode(['test' => 4]);

        $activityTypeContentType = new ActivityTypeContentType();
        $activityTypeContentType->setActivityType($activityType);
        $activityTypeContentType->setContentType($contentType);
        $activityTypeContentType->setDefaultInstances(1);
        $activityTypeContentType->setJsonConfig($config);

        $this->assertEquals($activityType, $activityTypeContentType->getActivityType());
        $this->assertEquals($contentType, $activityTypeContentType->getContentType());
        $this->assertEquals(1, $activityTypeContentType->getDefaultInstances());
        $this->assertEquals($config, $activityTypeContentType->getJsonConfig());

        $this->assertEquals(4, $activityTypeContentType->getConfig('test'));
    }
}
