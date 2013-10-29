<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Service\UserRelationshipService;
use EcampCore\Entity\UserRelationship;
use EcampCoreTest\Mock\AclMock;
use EcampCoreTest\Mock\EntityManagerMock;
use EcampCoreTest\Bootstrap;
use Doctrine\ORM\EntityManager;

class UserRelationshipServiceTest extends \PHPUnit_Framework_TestCase
{

    private function getAcl()
    {
        return new AclMock();
    }

    private function createEm()
    {
        return EntityManagerMock::createMock(Bootstrap::getServiceManager());
    }

    private function getUserRelationshipService(EntityManager $em)
    {
        $urRepo = $em->getRepository('EcampCore\Entity\UserRelationship');

        $UserRelationshipService =  new UserRelationshipService($urRepo);
        $UserRelationshipService->setEntityManager($em);
        $UserRelationshipService->setAcl($this->getAcl());

        return $UserRelationshipService;
    }

    public function testRequestRelationship()
    {
        $em = $this->createEm();
        $UserRelationshipService = $this->getUserRelationshipService($em);

        $a = new User();
        $b = new User();

        $this->assertTrue(
            $UserRelationshipService->RequestFriendship($a, $b));
        $this->assertFalse(
                $UserRelationshipService->RequestFriendship($a, $b));

        $this->assertTrue($a->userRelationship()->hasSentFriendshipRequestTo($b));
    }

    public function testRevokeRelationshipRequest()
    {
        $em = $this->createEm();
        $UserRelationshipService = $this->getUserRelationshipService($em);

        $em->persist($a = new User());
        $em->persist($b = new User());

        $UserRelationshipService->RequestFriendship($a, $b);
        $em->flush();

        $this->assertTrue(
            $UserRelationshipService->RevokeFriendshipRequest($a, $b));
        $this->assertFalse(
            $UserRelationshipService->RevokeFriendshipRequest($a, $b));

        $this->assertFalse($a->userRelationship()->isFriend($b));
        $this->assertTrue($a->userRelationship()->canSendFriendshipRequest($b));
        $this->assertTrue($b->userRelationship()->canSendFriendshipRequest($a));
    }

    public function testAcceptRelationshipRequest()
    {
        $em = $this->createEm();
        $UserRelationshipService = $this->getUserRelationshipService($em);

        $em->persist($a = new User());
        $em->persist($b = new User());

        $UserRelationshipService->RequestFriendship($b, $a);
        $em->flush();

        $this->assertTrue(
            $UserRelationshipService->AcceptFriendshipRequest($a, $b));
        $this->assertFalse(
            $UserRelationshipService->AcceptFriendshipRequest($a, $b));

        $this->assertTrue($a->userRelationship()->isFriend($b));
    }

    public function testRejectRelationshipRequest()
    {
        $em = $this->createEm();
        $UserRelationshipService = $this->getUserRelationshipService($em);

        $em->persist($a = new User());
        $em->persist($b = new User());

        $UserRelationshipService->RequestFriendship($b, $a);
        $em->flush();

        $this->assertTrue(
            $UserRelationshipService->RejectFriendshipRequest($a, $b));
        $this->assertFalse(
            $UserRelationshipService->RejectFriendshipRequest($a, $b));

        $this->assertFalse($a->userRelationship()->isFriend($b));
        $this->assertTrue($a->userRelationship()->canSendFriendshipRequest($b));
        $this->assertTrue($b->userRelationship()->canSendFriendshipRequest($a));
    }

    public function testTerminateRelationship()
    {
        $em = $this->createEm();
        $UserRelationshipService = $this->getUserRelationshipService($em);

        $em->persist($a = new User());
        $em->persist($b = new User());

        $UserRelationshipService->RequestFriendship($a, $b);
        $em->flush();

        $UserRelationshipService->AcceptFriendshipRequest($b, $a);
        $em->flush();

        $this->assertTrue($a->userRelationship()->isFriend($b));
        $this->assertTrue($b->userRelationship()->isFriend($a));

        $this->assertTrue(
            $UserRelationshipService->TerminateFriendship($a, $b));
        $this->assertFalse(
            $UserRelationshipService->TerminateFriendship($a, $b));
        $em->flush();

        $this->assertFalse($a->userRelationship()->isFriend($b));
        $this->assertFalse($b->userRelationship()->isFriend($a));

    }

}
