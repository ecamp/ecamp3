<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityTest extends AbstractTestCase {
    public function testActivityCategory() {
        $camp = new Camp();

        $activityType = new ActivityType();
        $activityType->setDefaultColor('#FF00FF');
        $activityType->setDefaultNumberingStyle('i');

        $activityCategory = new ActivityCategory();
        $activityCategory->setActivityType($activityType);

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('ActivityTitle');
        $activity->setActivityCategory($activityCategory);

        $this->assertEquals($camp, $activity->getCamp());
        $this->assertEquals('ActivityTitle', $activity->getTitle());
        $this->assertEquals($activityCategory, $activity->getActivityCategory());
        $this->assertEquals($activityType, $activity->getActivityType());
    }

    public function testActivityContent() {
        $activity = new Activity();
        $activityContent = new ActivityContent();

        $this->assertEquals(0, $activity->getActivityContents()->count());
        $activity->addActivityContent($activityContent);
        $this->assertContains($activityContent, $activity->getActivityContents());
        $activity->removeActivityContent($activityContent);
        $this->assertEquals(0, $activity->getActivityContents()->count());
    }

    public function testScheduleEntry() {
        $activity = new Activity();
        $scheduleEntry = new ScheduleEntry();

        $this->assertEquals(0, $activity->getScheduleEntries()->count());
        $activity->addScheduleEntry($scheduleEntry);
        $this->assertContains($scheduleEntry, $activity->getScheduleEntries());
        $activity->removeScheduleEntry($scheduleEntry);
        $this->assertEquals(0, $activity->getScheduleEntries()->count());
    }

    public function testCreateActivityContents() {
        $contentType = new ContentType();
        $contentType->setName('TestContentType');

        $activityTypeContentType = new ActivityTypeContentType();
        $activityTypeContentType->setContentType($contentType);
        $activityTypeContentType->setDefaultInstances(1);

        $camp = new Camp();

        $activityType = new ActivityType();
        $activityType->setDefaultColor('#FF00FF');
        $activityType->setDefaultNumberingStyle('i');
        $activityType->addActivityTypeContentType($activityTypeContentType);

        $activityCategory = new ActivityCategory();
        $activityCategory->setActivityType($activityType);

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('ActivityTitle');
        $activity->setActivityCategory($activityCategory);

        $activity->PrePersist();

        $this->assertCount(1, $activity->getActivityContents());
    }
}
