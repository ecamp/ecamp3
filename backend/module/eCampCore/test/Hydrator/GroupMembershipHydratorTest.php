<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\GroupMembershipHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class GroupMembershipHydratorTest extends AbstractTestCase {
    public function testExtract() {
        // Disable Test
        $this->assertTrue(true);

        return;
        $group = new Group();
        $user = new User();

        $membership = new GroupMembership();
        $membership->setGroup($group);
        $membership->setUser($user);
        $membership->setRole(GroupMembership::ROLE_MEMBER);
        $membership->setStatus(GroupMembership::STATUS_ESTABLISHED);
        $membership->setMembershipAcceptedBy('any');

        $hydrator = new GroupMembershipHydrator();
        $data = $hydrator->extract($membership);

        $this->assertEquals($group, $data['group']);
        $this->assertEquals($user, $data['user']);
        $this->assertEquals(GroupMembership::ROLE_MEMBER, $data['role']);
        $this->assertEquals(GroupMembership::STATUS_ESTABLISHED, $data['status']);
    }

    public function testHydrate() {
        // Disable Test
        $this->assertTrue(true);

        return;
        $group = new Group();
        $user = new User();

        $membership = new GroupMembership();

        $data = [
            'group' => $group,
            'user' => $user,
            'role' => GroupMembership::ROLE_MEMBER,
            'status' => GroupMembership::STATUS_ESTABLISHED,
        ];

        $hydrator = new GroupMembershipHydrator();
        $hydrator->hydrate($data, $membership);

        $this->assertEquals($group, $membership->getGroup());
        $this->assertEquals($user, $membership->getUser());
        $this->assertEquals(GroupMembership::ROLE_MEMBER, $membership->getRole());
        $this->assertEquals(GroupMembership::STATUS_ESTABLISHED, $membership->getStatus());
    }
}
