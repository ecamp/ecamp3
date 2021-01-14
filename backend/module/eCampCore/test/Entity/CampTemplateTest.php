<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\CampTemplate;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampTemplateTest extends AbstractTestCase {
    public function testCampType() {
        $campTemplate = new CampTemplate();
        $campTemplate->setName('CampType.Name');

        $this->assertEquals('CampType.Name', $campTemplate->getName());
    }

    public function testActivityCategoryTemplate() {
        $campTemplate = new CampTemplate();
        $activityCategoryTemplate = new ActivityCategoryTemplate();

        $this->assertCount(0, $campTemplate->getActivityCategoryTemplate());
        $campTemplate->addActivityCategoryTemplate($activityCategoryTemplate);
        $this->assertContains($activityCategoryTemplate, $campTemplate->getActivityCategoryTemplate());
        $campTemplate->removeActivityCategoryTemplate($activityCategoryTemplate);
        $this->assertCount(0, $campTemplate->getActivityCategoryTemplate());
    }
}
