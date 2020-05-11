<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Job;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Zend\Json\Json;

/**
 * @internal
 */
class CampTest extends AbstractTestCase {
    public function testCamp() {
        $user = new User();
        $user->setUsername('username');

        $organization = new Organization();
        $organization->setName('PBS');

        $config = Json::encode(['test' => 3]);
        $campType = new CampType();
        $campType->setName('CampType.Name');
        $campType->setOrganization($organization);
        $campType->setIsJS(true);
        $campType->setIsCourse(true);
        $campType->setJsonConfig($config);

        $camp = new Camp();
        $camp->setCampType($campType);
        $camp->setOwner($user);
        $camp->setName('TestCamp');
        $camp->setTitle('TestTitle');
        $camp->setMotto('TestMotto');
        $camp->setCreator($user);

        $this->assertEquals($campType, $camp->getCampType());
        $this->assertEquals($user, $camp->getOwner());
        $this->assertEquals('TestCamp', $camp->getName());
        $this->assertEquals('TestTitle', $camp->getTitle());
        $this->assertEquals('TestMotto', $camp->getMotto());
        $this->assertEquals($user, $camp->getCreator());
        $this->assertEquals(3, $camp->getConfig('test'));

        $this->assertFalse($camp->belongsToGroup());
        $this->assertTrue($camp->belongsToUser());
    }

    public function testPeriod() {
        $camp = new Camp();
        $period = new Period();

        $this->assertEquals(0, $camp->getPeriods()->count());
        $camp->addPeriod($period);
        $this->assertContains($period, $camp->getPeriods());
        $camp->removePeriod($period);
        $this->assertEquals(0, $camp->getPeriods()->count());
    }

    public function testCampCollaboration() {
        $camp = new Camp();
        $collaboration = new CampCollaboration();

        $this->assertEquals(0, $camp->getCampCollaborations()->count());
        $camp->addCampCollaboration($collaboration);
        $this->assertContains($collaboration, $camp->getCampCollaborations());
        $camp->removeCampCollaboration($collaboration);
        $this->assertEquals(0, $camp->getCampCollaborations()->count());
    }

    public function testJob() {
        $camp = new Camp();
        $job = new Job();

        $this->assertEquals(0, $camp->getJobs()->count());
        $camp->addJob($job);
        $this->assertContains($job, $camp->getJobs());
        $camp->removeJob($job);
        $this->assertEquals(0, $camp->getJobs()->count());
    }

    public function testActivityCategory() {
        $camp = new Camp();
        $category = new ActivityCategory();

        $this->assertEquals(0, $camp->getActivityCategories()->count());
        $camp->addActivityCategory($category);
        $this->assertContains($category, $camp->getActivityCategories());
        $camp->removeActivityCategory($category);
        $this->assertEquals(0, $camp->getActivityCategories()->count());
    }

    public function testActivity() {
        $camp = new Camp();
        $activity = new Activity();

        $this->assertEquals(0, $camp->getActivities()->count());
        $camp->addActivity($activity);
        $this->assertContains($activity, $camp->getActivities());
        $camp->removeActivity($activity);
        $this->assertEquals(0, $camp->getActivities()->count());
    }
}
