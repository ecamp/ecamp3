<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\CampTemplate;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampTemplateTest extends AbstractTestCase {
    public function testCampTemplate() {
        $campTemplate = new CampTemplate();
        $campTemplate->setName('CampTemplate.Name');

        $this->assertEquals('CampTemplate.Name', $campTemplate->getName());
    }

    public function testActivityCategoryTemplate() {
        $campTemplate = new CampTemplate();
        $activityCategoryTemplate = new ActivityCategoryTemplate();

        $this->assertCount(0, $campTemplate->getActivityCategoryTemplates());
        $campTemplate->addActivityCategoryTemplate($activityCategoryTemplate);
        $this->assertContains($activityCategoryTemplate, $campTemplate->getActivityCategoryTemplates());
        $campTemplate->removeActivityCategoryTemplate($activityCategoryTemplate);
        $this->assertCount(0, $campTemplate->getActivityCategoryTemplates());
    }
}
