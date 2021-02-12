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
        $activityContent->setPosition('position');

        $this->assertEquals($activity, $activityContent->getActivity());
        $this->assertEquals($contentType, $activityContent->getContentType());
        $this->assertEquals('ActivityContentName', $activityContent->getInstanceName());
        $this->assertEquals('position', $activityContent->getPosition());
        $this->assertEquals($camp, $activityContent->getCamp());
    }

    public function testActivityContentHierarchy() {
        $activityContent = new ActivityContent();
        $childActivityContent = new ActivityContent();

        // Add Child-ActivityContent
        $activityContent->addChild($childActivityContent);
        $this->assertCount(1, $activityContent->getChildren());
        $this->assertEquals($activityContent, $childActivityContent->getParent());

        // Remove Child-ActivityContent
        $activityContent->removeChild($childActivityContent);
        $this->assertCount(0, $activityContent->getChildren());
        $this->assertNull($childActivityContent->getParent());
    }
}
