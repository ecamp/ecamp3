<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampTest extends AbstractTestCase {
    public function testCamp(): void {
        $user = new User();
        $user->setUsername('username');

        $camp = new Camp();
        $camp->setOwner($user);
        $camp->setName('TestCamp');
        $camp->setTitle('TestTitle');
        $camp->setMotto('TestMotto');
        $camp->setAddressName('AdrName');
        $camp->setAddressStreet('AdrStreet');
        $camp->setAddressZipcode('AdrZipcode');
        $camp->setAddressCity('AdrCity');
        $camp->setCreator($user);

        $this->assertEquals($user, $camp->getOwner());
        $this->assertEquals('TestCamp', $camp->getName());
        $this->assertEquals('TestTitle', $camp->getTitle());
        $this->assertEquals('TestMotto', $camp->getMotto());
        $this->assertEquals('AdrName', $camp->getAddressName());
        $this->assertEquals('AdrStreet', $camp->getAddressStreet());
        $this->assertEquals('AdrZipcode', $camp->getAddressZipcode());
        $this->assertEquals('AdrCity', $camp->getAddressCity());
        $this->assertEquals($user, $camp->getCreator());

        $this->assertFalse($camp->belongsToGroup());
        $this->assertTrue($camp->belongsToUser());
    }

    public function testPeriod(): void {
        $camp = new Camp();
        $period = new Period();

        $this->assertEquals(0, $camp->getPeriods()->count());
        $camp->addPeriod($period);
        $this->assertContains($period, $camp->getPeriods());
        $camp->removePeriod($period);
        $this->assertEquals(0, $camp->getPeriods()->count());
    }

    public function testCampCollaboration(): void {
        $camp = new Camp();
        $collaboration = new CampCollaboration();

        $this->assertEquals(0, $camp->getCampCollaborations()->count());
        $camp->addCampCollaboration($collaboration);
        $this->assertContains($collaboration, $camp->getCampCollaborations());
        $camp->removeCampCollaboration($collaboration);
        $this->assertEquals(0, $camp->getCampCollaborations()->count());
    }

    public function testCategory(): void {
        $camp = new Camp();
        $category = new Category();

        $this->assertEquals(0, $camp->getCategories()->count());
        $camp->addCategory($category);
        $this->assertContains($category, $camp->getCategories());
        $camp->removeCategory($category);
        $this->assertEquals(0, $camp->getCategories()->count());
    }

    public function testActivity(): void {
        $camp = new Camp();
        $activity = new Activity();

        $this->assertEquals(0, $camp->getActivities()->count());
        $camp->addActivity($activity);
        $this->assertContains($activity, $camp->getActivities());
        $camp->removeActivity($activity);
        $this->assertEquals(0, $camp->getActivities()->count());
    }
}
