<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\ActivityTypeContentType;
use eCamp\Core\Entity\ActivityTypeFactory;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ActivityTypeTest extends AbstractTestCase {
    public function testActivityType() {
        $activityType = new ActivityType();
        $activityType->setName('ActivityType Name');
        $activityType->setDefaultColor('#FF00FF');
        $activityType->setDefaultNumberingStyle('i');

        $this->assertEquals('ActivityType Name', $activityType->getName());
        $this->assertEquals('#FF00FF', $activityType->getDefaultColor());
        $this->assertEquals('i', $activityType->getDefaultNumberingStyle());
        // $this->assertEquals('name', $activityType->getCampTypes()->get(0)->getName());
        // $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $activityType->getCampTypes());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $activityType->getActivityTypeContentTypes());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $activityType->getActivityTypeFactories());
    }

    public function testActivityTypeFactory() {
        $activityType = new ActivityType();
        $activityTypeFactory = new ActivityTypeFactory();

        $this->assertEquals(0, $activityType->getActivityTypeFactories()->count());
        $activityType->addActivityTypeFactory($activityTypeFactory);
        $this->assertContains($activityTypeFactory, $activityType->getActivityTypeFactories());
        $activityType->removeActivityTypeFactory($activityTypeFactory);
        $this->assertEquals(0, $activityType->getActivityTypeFactories()->count());
    }

    public function testActivityTypeContentType() {
        $activityType = new ActivityType();
        $activityTypeContentType = new ActivityTypeContentType();

        $this->assertEquals(0, $activityType->getActivityTypeContentTypes()->count());
        $activityType->addActivityTypeContentType($activityTypeContentType);
        $this->assertContains($activityTypeContentType, $activityType->getActivityTypeContentTypes());
        $activityType->removeActivityTypeContentType($activityTypeContentType);
        $this->assertEquals(0, $activityType->getActivityTypeContentTypes()->count());
    }
}
