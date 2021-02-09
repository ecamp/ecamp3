<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityContentTest extends AbstractTestCase {
    public function testActivityContent() {
        $camp = new Camp();

        $contentType = new ContentType();

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('ActivityTitle');

        $activityContent = new ActivityContent();
        $activityContent->setActivity($activity);
        $activityContent->setContentType($contentType);
        $activityContent->setInstanceName('ActivityContentName');

        $this->assertEquals($activity, $activityContent->getActivity());
        $this->assertEquals($contentType, $activityContent->getContentType());
        $this->assertEquals('ActivityContentName', $activityContent->getInstanceName());
        $this->assertEquals($camp, $activityContent->getCamp());
    }
}
