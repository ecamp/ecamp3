<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCoreTest\Mock\AclMock;
use EcampCoreTest\Mock\EntityManagerMock;
use EcampCoreTest\Bootstrap;
use Doctrine\ORM\EntityManager;
use EcampCore\Entity\GroupMembership;
use EcampCore\Service\GroupMembershipService;
use EcampCore\Entity\Group;

class GroupMembershipServiceTest extends \PHPUnit_Framework_TestCase
{

    private function getAcl()
    {
        return new AclMock();
    }

    private function createEm()
    {
        return EntityManagerMock::createMock(Bootstrap::getServiceManager());
    }

    /**
     * @return \EcampCore\Service\GroupMembershipService
     */
    private function getGroupMembershipService(EntityManager $em)
    {
        $gmRepo = $em->getRepository('EcampCore\Entity\GroupMembership');

        $groupMembershipService =  new GroupMembershipService($gmRepo);
        $groupMembershipService->setEntityManager($em);
        $groupMembershipService->setAcl($this->getAcl());

        return $groupMembershipService;
    }

    public function testRequestMembership()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->requestMembership($user, $group);

        $this->assertTrue($user->groupMembership()->hasSentMembershipRequestTo($group));
        $this->assertTrue($group->groupMembership()->hasReceivedMembershipRequestFrom($user));

        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertTrue($gm->isRequest());
        $this->assertEquals($user, $gm->getUser());
        $this->assertEquals($group, $gm->getGroup());

        $this->assertContains($gm, $user->groupMembership()->getSentMembershipRequests(GroupMembership::ROLE_MEMBER));
        $this->assertContains($gm, $group->groupMembership()->getReceivedMembershipRequests(GroupMembership::ROLE_MEMBER));
    }

    public function testRevokeRequest()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->requestMembership($user, $group, GroupMembership::ROLE_MANAGER);
        $em->flush();

        $groupMembershipService->revokeRequest($user, $group);
        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertNull($gm);

        $this->assertTrue($user->groupMembership()->canSendMembershipRequest($group));
        $this->assertTrue($group->groupMembership()->canSendMembershipInvitation($user));
    }

    public function testAcceptRequest()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->requestMembership($user, $group, GroupMembership::ROLE_MANAGER);
        $em->flush();

        $groupMembershipService->acceptRequest($manager, $group, $user, GroupMembership::ROLE_MEMBER);
        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertTrue($gm->isEstablished());
        $this->assertFalse($user->groupMembership()->hasSentMembershipRequestTo($group));
        $this->assertFalse($group->groupMembership()->hasReceivedMembershipRequestFrom($user));

        $this->assertTrue($user->groupMembership()->isMemberOf($group));
        $this->assertTrue($group->groupMembership()->hasMember($user));

        $this->assertEquals($gm, $user->groupMembership()->getMembership($group));
        $this->assertEquals($gm, $group->groupMembership()->getMembership($user));
    }

    public function testRejectRequest()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->requestMembership($user, $group, GroupMembership::ROLE_MANAGER);
        $em->flush();

        $groupMembershipService->rejectRequest($group, $user);
        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertNull($gm);

        $this->assertTrue($user->groupMembership()->canSendMembershipRequest($group));
        $this->assertTrue($group->groupMembership()->canSendMembershipInvitation($user));
    }

    public function testInviteUser()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->inviteUser($manager, $group, $user, GroupMembership::ROLE_MANAGER);

        $this->assertTrue($user->groupMembership()->hasReceivedMembershipInvitationFrom($group));
        $this->assertTrue($group->groupMembership()->hasSentMembershipInvitationTo($user));

        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertTrue($gm->isInvitation());
        $this->assertEquals(GroupMembership::ROLE_MANAGER, $gm->getRole());
        $this->assertEquals($user, $gm->getUser());
        $this->assertEquals($group, $gm->getGroup());

        $this->assertContains($gm, $user->groupMembership()->getReceivedMembershipInvitations(GroupMembership::ROLE_MANAGER));
        $this->assertContains($gm, $group->groupMembership()->getSentMembershipInvitations(GroupMembership::ROLE_MANAGER));
    }

    public function testRevokeInvitation()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->inviteUser($manager, $group, $user, GroupMembership::ROLE_MANAGER);
        $em->flush();

        $groupMembershipService->revokeInvitation($group, $user);
        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertNull($gm);

        $this->assertTrue($user->groupMembership()->canSendMembershipRequest($group));
        $this->assertTrue($group->groupMembership()->canSendMembershipInvitation($user));
    }

    public function testAcceptInvitation()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->inviteUser($manager, $group, $user, GroupMembership::ROLE_MANAGER);
        $em->flush();

        $groupMembershipService->acceptInvitation($user, $group);
        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertTrue($gm->isEstablished());
        $this->assertFalse($user->groupMembership()->hasSentMembershipRequestTo($group));
        $this->assertFalse($group->groupMembership()->hasReceivedMembershipRequestFrom($user));

        $this->assertTrue($user->groupMembership()->isMemberOf($group));
        $this->assertTrue($group->groupMembership()->hasMember($user));

        $this->assertEquals($gm, $user->groupMembership()->getMembership($group));
        $this->assertEquals($gm, $group->groupMembership()->getMembership($user));
    }

    public function testRejectInvitation()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->inviteUser($manager, $group, $user, GroupMembership::ROLE_MANAGER);
        $em->flush();

        $groupMembershipService->rejectInvitation($user, $group);
        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertNull($gm);

        $this->assertTrue($user->groupMembership()->canSendMembershipRequest($group));
        $this->assertTrue($group->groupMembership()->canSendMembershipInvitation($user));
    }

    public function testLeaveGroup()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->requestMembership($user, $group);
        $em->flush();
        $groupMembershipService->acceptRequest($manager, $group, $user);
        $em->flush();

        $groupMembershipService->leaveGroup($user, $group);
        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertNull($gm);

        $this->assertTrue($user->groupMembership()->canSendMembershipRequest($group));
        $this->assertTrue($group->groupMembership()->canSendMembershipInvitation($user));
    }

    public function testKickUser()
    {
        $em = $this->createEm();
        $groupMembershipService = $this->getGroupMembershipService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($group = new Group());
        $group->setName('pbs');
        $group->setDescription('PBS');

        $groupMembershipService->requestMembership($user, $group);
        $em->flush();
        $groupMembershipService->acceptRequest($manager, $group, $user);
        $em->flush();

        $groupMembershipService->kickUser($group, $user);
        $em->flush();

        $gm = $em->getRepository('EcampCore\Entity\GroupMembership')->findByGroupAndUser($group, $user);

        $this->assertNull($gm);

        $this->assertTrue($user->groupMembership()->canSendMembershipRequest($group));
        $this->assertTrue($group->groupMembership()->canSendMembershipInvitation($user));
    }

}
