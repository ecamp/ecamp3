<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\ContentTypeConfigTemplate;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityCategoryTemplateTest extends AbstractTestCase {
    public function testActivityTemplate() {
        $activityCategoryTemplate = new ActivityCategoryTemplate();
        $activityCategoryTemplate->setName('ActivityType Name');
        $activityCategoryTemplate->setColor('#FF00FF');
        $activityCategoryTemplate->setNumberingStyle('i');

        $this->assertEquals('ActivityType Name', $activityCategoryTemplate->getName());
        $this->assertEquals('#FF00FF', $activityCategoryTemplate->getColor());
        $this->assertEquals('i', $activityCategoryTemplate->getNumberingStyle());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $activityCategoryTemplate->getContentTypeConfigTemplates());
    }

    public function testActivityTypeContentType() {
        $activityCategoryTemplate = new ActivityCategoryTemplate();
        $contentTypeConfigTemplate = new ContentTypeConfigTemplate();

        $this->assertCount(0, $activityCategoryTemplate->getContentTypeConfigTemplates());
        $activityCategoryTemplate->addContentTypeConfigTemplate($contentTypeConfigTemplate);
        $this->assertContains($contentTypeConfigTemplate, $activityCategoryTemplate->getContentTypeConfigTemplates());
        $activityCategoryTemplate->removeContentTypeConfigTemplate($contentTypeConfigTemplate);
        $this->assertCount(0, $activityCategoryTemplate->getContentTypeConfigTemplates());
    }
}
