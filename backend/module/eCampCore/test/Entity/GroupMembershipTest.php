<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class GroupMembershipTest extends AbstractTestCase {
    public function testGroupMembership() {
        $group = new Group();
        $user = new User();

        $membership = new GroupMembership();
        $membership->setGroup($group);
        $membership->setUser($user);
        $membership->setRole(GroupMembership::ROLE_MEMBER);
        $membership->setStatus(GroupMembership::STATUS_ESTABLISHED);
        $membership->setMembershipAcceptedBy('install');

        $this->assertEquals($group, $membership->getGroup());
        $this->assertEquals($user, $membership->getUser());
        $this->assertEquals(GroupMembership::ROLE_MEMBER, $membership->getRole());
        $this->assertEquals(GroupMembership::STATUS_ESTABLISHED, $membership->getStatus());
        $this->assertEquals('install', $membership->getMembershipAcceptedBy());
    }

    public function testStatus() {
        $membership = new GroupMembership();
        $membership->setStatus(GroupMembership::STATUS_REQUESTED);
        $this->assertTrue($membership->isRequest());

        $membership->setStatus(GroupMembership::STATUS_INVITED);
        $this->assertTrue($membership->isInvitation());

        $membership->setStatus(GroupMembership::STATUS_ESTABLISHED);
        $this->assertTrue($membership->isEstablished());

        $this->expectException('Exception');
        $membership->setStatus('test');
    }

    public function testRole() {
        $membership = new GroupMembership();
        $membership->setRole(GroupMembership::ROLE_GUEST);
        $this->assertTrue($membership->isGuest());

        $membership->setRole(GroupMembership::ROLE_MEMBER);
        $this->assertTrue($membership->isMember());

        $membership->setRole(GroupMembership::ROLE_MANAGER);
        $this->assertTrue($membership->isManager());

        $this->expectException('Exception');
        $membership->setRole('test');
    }

    public function testLifecycle() {
        $membership = new GroupMembership();
        $membership->setStatus(GroupMembership::STATUS_REQUESTED);
        $membership->setMembershipAcceptedBy('install');

        $membership->PrePersist();
        $membership->PreUpdate();

        $this->assertNull($membership->getMembershipAcceptedBy());
    }
}
