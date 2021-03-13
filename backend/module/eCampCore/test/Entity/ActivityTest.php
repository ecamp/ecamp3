<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityResponsible;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentNode;
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

    public function testContentNode(): void {
        $activity = new Activity();
        $root = new ContentNode();
        $node = new ContentNode();
        $node->setParent($root);

        $this->assertCount(0, $activity->getAllContentNodes());
        $activity->setRootContentNode($root);
        $this->assertContains($root, $activity->getAllContentNodes());
        $this->assertContains($node, $activity->getAllContentNodes());
        $activity->setRootContentNode(null);
        $this->assertCount(0, $activity->getAllContentNodes());
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

    public function testActivityResponsible(): void {
        $activity = new Activity();
        $activityResponsible = new ActivityResponsible();

        $this->assertEquals(0, $activity->getActivityResponsibles()->count());
        $activity->addActivityResponsible($activityResponsible);
        $this->assertContains($activityResponsible, $activity->getActivityResponsibles());
        $activity->removeActivityResponsible($activityResponsible);
        $this->assertEquals(0, $activity->getActivityResponsibles()->count());
    }
}
