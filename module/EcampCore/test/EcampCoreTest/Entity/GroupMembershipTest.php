<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;

class GroupMembershipTest extends \PHPUnit_Framework_TestCase
{

    public function testNotAMembership()
    {
        $user = new User();
        $group = new Group();

        $this->assertFalse($user->groupMembership()->isMemberOf($group));
        $this->assertFalse($group->groupMembership()->hasMember($user));
    }

    public function testMembershipRequest()
    {
        $user = new User();
        $manager = new User();
        $group = new Group();

        $userGroupMembership =
            GroupMembership::createRequest($user, $group);
        $managerGroupMembership =
            GroupMembership::createRequest($manager, $group, GroupMembership::ROLE_MANAGER);

        $this->assertEquals(GroupMembership::ROLE_MEMBER, $userGroupMembership->getRole());
        $this->assertEquals(GroupMembership::ROLE_MANAGER, $managerGroupMembership->getRole());

        $this->assertEquals(GroupMembership::STATUS_REQUESTED, $userGroupMembership->getStatus());
        $this->assertEquals(GroupMembership::STATUS_REQUESTED, $managerGroupMembership->getStatus());

        $this->assertTrue($user->groupMembership()->hasSentMembershipRequestTo($group));
        $this->assertFalse($user->groupMembership()->hasReceivedMembershipInvitationFrom($group));
        $this->assertTrue($manager->groupMembership()->hasSentMembershipRequestTo($group));
        $this->assertFalse($manager->groupMembership()->hasReceivedMembershipInvitationFrom($group));

        $this->assertTrue($group->groupMembership()->hasReceivedMembershipRequestFrom($user));
        $this->assertFalse($group->groupMembership()->hasSentMembershipInvitationTo($user));
        $this->assertTrue($group->groupMembership()->hasReceivedMembershipRequestFrom($manager));
        $this->assertFalse($group->groupMembership()->hasSentMembershipInvitationTo($manager));

        $this->assertContains($userGroupMembership, $group->groupMembership()->getReceivedMembershipRequests());
        $this->assertContains($managerGroupMembership, $group->groupMembership()->getReceivedMembershipRequests());

        $userGroupMembership->acceptRequest($manager);
        $managerGroupMembership->acceptRequest($manager);

        $this->assertEquals(GroupMembership::STATUS_ESTABLISHED, $userGroupMembership->getStatus());
        $this->assertEquals(GroupMembership::STATUS_ESTABLISHED, $managerGroupMembership->getStatus());

        $this->assertTrue($userGroupMembership->isEstablished());
        $this->assertTrue($userGroupMembership->isEstablished());

        $this->assertEquals($userGroupMembership, $user->groupMembership()->getMembership($group));
        $this->assertEquals($userGroupMembership, $group->groupMembership()->getMembership($user));
        $this->assertEquals($managerGroupMembership, $manager->groupMembership()->getMembership($group));
        $this->assertEquals($managerGroupMembership, $group->groupMembership()->getMembership($manager));

        $this->assertTrue($user->groupMembership()->isMemberOf($group));
        $this->assertTrue($manager->groupMembership()->isMemberOf($group));

        $this->assertTrue($group->groupMembership()->isMember($user));
        $this->assertTrue($group->groupMembership()->isManager($manager));
    }

    public function testMembershipInvitation()
    {
        $user = new User();
        $manager = new User();
        $group = new Group();

        $userGroupMembership =
            GroupMembership::createInvitation($user, $group, $manager);
        $managerGroupMembership =
            GroupMembership::createInvitation($manager, $group, $manager, GroupMembership::ROLE_MANAGER);

        $this->assertEquals(GroupMembership::STATUS_INVITED, $userGroupMembership->getStatus());
        $this->assertEquals(GroupMembership::STATUS_INVITED, $managerGroupMembership->getStatus());

        $this->assertFalse($user->groupMembership()->hasSentMembershipRequestTo($group));
        $this->assertTrue($user->groupMembership()->hasReceivedMembershipInvitationFrom($group));
        $this->assertFalse($manager->groupMembership()->hasSentMembershipRequestTo($group));
        $this->assertTrue($manager->groupMembership()->hasReceivedMembershipInvitationFrom($group));

        $this->assertFalse($group->groupMembership()->hasReceivedMembershipRequestFrom($user));
        $this->assertTrue($group->groupMembership()->hasSentMembershipInvitationTo($user));
        $this->assertFalse($group->groupMembership()->hasReceivedMembershipRequestFrom($manager));
        $this->assertTrue($group->groupMembership()->hasSentMembershipInvitationTo($manager));

        $this->assertTrue($userGroupMembership->isInvitation());
        $this->assertTrue($managerGroupMembership->isInvitation());

        $userGroupMembership->acceptInvitation();
        $managerGroupMembership->acceptInvitation();

        $this->assertEquals(GroupMembership::STATUS_ESTABLISHED, $userGroupMembership->getStatus());
        $this->assertEquals(GroupMembership::STATUS_ESTABLISHED, $managerGroupMembership->getStatus());

        $this->assertTrue($userGroupMembership->isEstablished());
        $this->assertTrue($userGroupMembership->isEstablished());

        $this->assertEquals($userGroupMembership, $user->groupMembership()->getMembership($group));
        $this->assertEquals($userGroupMembership, $group->groupMembership()->getMembership($user));
        $this->assertEquals($managerGroupMembership, $manager->groupMembership()->getMembership($group));
        $this->assertEquals($managerGroupMembership, $group->groupMembership()->getMembership($manager));

        $this->assertTrue($user->groupMembership()->isMemberOf($group));
        $this->assertTrue($manager->groupMembership()->isMemberOf($group));

        $this->assertTrue($group->groupMembership()->isMember($user));
        $this->assertTrue($group->groupMembership()->isManager($manager));
    }

}
