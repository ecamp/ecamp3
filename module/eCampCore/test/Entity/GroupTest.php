<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\Organization;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class GroupTest extends AbstractTestCase {
    public function testGroup() {
        $organization = new Organization();

        $group = new Group();
        $group->setOrganization($organization);
        $group->setName('GroupName');
        $group->setDescription('GroupDesc');
        $group->setParent(null);

        $this->assertEquals($organization, $group->getOrganization());
        $this->assertEquals('GroupName', $group->getName());
        $this->assertEquals('GroupName', $group->getDisplayName());
        $this->assertEquals('GroupDesc', $group->getDescription());
        $this->assertNull($group->getParent());
    }

    public function testParentGroup() {
        $organization = new Organization();

        $group1 = new Group();
        $group1->setOrganization($organization);

        $group2 = new Group();
        $group2->setParent($group1);
        $this->assertEquals($organization, $group2->getOrganization());

        $path = $group2->pathAsArray();
        $this->assertContains($group1, $path);
        $this->assertContains($group2, $path);


        $group3 = new Group();

        $this->assertEquals(0, $group2->getChildren()->count());
        $group2->addChild($group3);
        $this->assertContains($group3, $group2->getChildren());
        $group2->removeChild($group3);
        $this->assertEquals(0, $group2->getChildren()->count());
    }

    public function testCamp() {
        $camp = new Camp();
        $group = new Group();

        $this->assertEquals(0, $group->getOwnedCamps()->count());
        $group->addOwnedCamp($camp);
        $this->assertContains($camp, $group->getOwnedCamps());
        $group->removeOwnedCamp($camp);
        $this->assertEquals(0, $group->getOwnedCamps()->count());
    }

    public function testMembership() {
        $membership = new GroupMembership();
        $group = new Group();

        $this->assertEquals(0, $group->getGroupMemberships()->count());
        $group->addGroupMembership($membership);
        $this->assertContains($membership, $group->getGroupMemberships());
        $group->removeGroupMembership($membership);
        $this->assertEquals(0, $group->getGroupMemberships()->count());
    }
}
