<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Zend\Json\Json;

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
        $activityTypeContentType->setMinNumberContentTypeInstances(1);
        $activityTypeContentType->setMaxNumberContentTypeInstances(3);
        $activityTypeContentType->setJsonConfig($config);

        $this->assertEquals($activityType, $activityTypeContentType->getActivityType());
        $this->assertEquals($contentType, $activityTypeContentType->getContentType());
        $this->assertEquals(1, $activityTypeContentType->getMinNumberContentTypeInstances());
        $this->assertEquals(3, $activityTypeContentType->getMaxNumberContentTypeInstances());
        $this->assertEquals($config, $activityTypeContentType->getJsonConfig());

        $this->assertEquals(4, $activityTypeContentType->getConfig('test'));
    }
}
