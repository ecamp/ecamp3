<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityTest extends AbstractTestCase {
    public function testCategory(): void {
        $camp = new Camp();
        $category = new Category();

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('ActivityTitle');
        $activity->setCategory($category);

        $this->assertEquals($camp, $activity->getCamp());
        $this->assertEquals('ActivityTitle', $activity->getTitle());
        $this->assertEquals($category, $activity->getCategory());
    }

    public function testActivityContent(): void {
        $activity = new Activity();
        $activityContent = new ActivityContent();

        $this->assertEquals(0, $activity->getActivityContents()->count());
        $activity->addActivityContent($activityContent);
        $this->assertContains($activityContent, $activity->getActivityContents());
        $activity->removeActivityContent($activityContent);
        $this->assertEquals(0, $activity->getActivityContents()->count());
    }

    public function testScheduleEntry(): void {
        $activity = new Activity();
        $scheduleEntry = new ScheduleEntry();

        $this->assertEquals(0, $activity->getScheduleEntries()->count());
        $activity->addScheduleEntry($scheduleEntry);
        $this->assertContains($scheduleEntry, $activity->getScheduleEntries());
        $activity->removeScheduleEntry($scheduleEntry);
        $this->assertEquals(0, $activity->getScheduleEntries()->count());
    }
}
