<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\Job;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Zend\Json\Json;

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

        $group = new Group();
        $group->setOrganization($organization);
        $group->setName('GroupName');

        $camp = new Camp();
        $camp->setCampType($campType);
        $camp->setOwner($group);
        $camp->setName('TestCamp');
        $camp->setTitle('TestTitle');
        $camp->setMotto('TestMotto');
        $camp->setCreator($user);

        $this->assertEquals($campType, $camp->getCampType());
        $this->assertEquals($group, $camp->getOwner());
        $this->assertEquals('TestCamp', $camp->getName());
        $this->assertEquals('TestTitle', $camp->getTitle());
        $this->assertEquals('TestMotto', $camp->getMotto());
        $this->assertEquals($user, $camp->getCreator());
        $this->assertEquals(3, $camp->getConfig('test'));

        $this->assertTrue($camp->belongsToGroup());
        $this->assertFalse($camp->belongsToUser());
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

    public function testEventCategory() {
        $camp = new Camp();
        $category = new EventCategory();

        $this->assertEquals(0, $camp->getEventCategories()->count());
        $camp->addEventCategory($category);
        $this->assertContains($category, $camp->getEventCategories());
        $camp->removeEventCategory($category);
        $this->assertEquals(0, $camp->getEventCategories()->count());
    }

    public function testEvent() {
        $camp = new Camp();
        $event = new Event();

        $this->assertEquals(0, $camp->getEvents()->count());
        $camp->addEvent($event);
        $this->assertContains($event, $camp->getEvents());
        $camp->removeEvent($event);
        $this->assertEquals(0, $camp->getEvents()->count());
    }
}
