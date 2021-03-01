<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
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
        $contentnode = new ContentNode();

        $this->assertEquals(0, $activity->getContentNodes()->count());
        $activity->addContentNode($contentnode);
        $this->assertContains($contentnode, $activity->getContentNodes());
        $activity->removeContentNode($contentnode);
        $this->assertEquals(0, $activity->getContentNodes()->count());
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
