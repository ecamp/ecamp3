<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\ContentTypeConfigTemplate;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityCategoryTemplateTest extends AbstractTestCase {
    public function testActivityTemplate() {
        $campTemplate = new CampTemplate();
        $activityCategoryTemplate = new ActivityCategoryTemplate();
        $activityCategoryTemplate->setName('ActivityType Name');
        $activityCategoryTemplate->setColor('#FF00FF');
        $activityCategoryTemplate->setNumberingStyle('i');
        $campTemplate->addActivityCategoryTemplate($activityCategoryTemplate);

        $this->assertEquals($campTemplate, $activityCategoryTemplate->getCampTemplate());
        $this->assertEquals('ActivityType Name', $activityCategoryTemplate->getName());
        $this->assertEquals('#FF00FF', $activityCategoryTemplate->getColor());
        $this->assertEquals('i', $activityCategoryTemplate->getNumberingStyle());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $activityCategoryTemplate->getContentTypeConfigTemplates());
    }

    public function testContentTypeConfigTemplate() {
        $activityCategoryTemplate = new ActivityCategoryTemplate();
        $contentTypeConfigTemplate = new ContentTypeConfigTemplate();

        $this->assertCount(0, $activityCategoryTemplate->getContentTypeConfigTemplates());
        $activityCategoryTemplate->addContentTypeConfigTemplate($contentTypeConfigTemplate);
        $this->assertContains($contentTypeConfigTemplate, $activityCategoryTemplate->getContentTypeConfigTemplates());
        $activityCategoryTemplate->removeContentTypeConfigTemplate($contentTypeConfigTemplate);
        $this->assertCount(0, $activityCategoryTemplate->getContentTypeConfigTemplates());
    }
}
