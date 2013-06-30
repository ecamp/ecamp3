<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Service\RelationshipService;
use EcampCore\Entity\UserRelationship;
use EcampCoreTest\Mock\AclMock;
use EcampCoreTest\Mock\EntityManagerMock;
use EcampCoreTest\Bootstrap;
use Doctrine\ORM\EntityManager;

class RelationshipServiceTest extends \PHPUnit_Framework_TestCase
{

    private function getAcl()
    {
        return new AclMock();
    }

    private function createEm()
    {
        return EntityManagerMock::createMock(Bootstrap::getServiceManager());
    }

    private function getRelationshipService(EntityManager $em)
    {
        $urRepo = $em->getRepository('EcampCore\Entity\UserRelationship');

        $relationshipService =  new RelationshipService($urRepo);
        $relationshipService->setEntityManager($em);
        $relationshipService->setAcl($this->getAcl());

        return $relationshipService;
    }

    public function testRequestRelationship()
    {
        $em = $this->createEm();
        $relationshipService = $this->getRelationshipService($em);

        $a = new User();
        $b = new User();

        $this->assertTrue(
            $relationshipService->RequestFriendship($a, $b));
        $this->assertFalse(
                $relationshipService->RequestFriendship($a, $b));

        $this->assertTrue($a->userRelationship()->hasSentFriendshipRequestTo($b));
    }

    public function testRevokeRelationshipRequest()
    {
        $em = $this->createEm();
        $relationshipService = $this->getRelationshipService($em);

        $em->persist($a = new User());
        $em->persist($b = new User());

        $relationshipService->RequestFriendship($a, $b);
        $em->flush();

        $this->assertTrue(
            $relationshipService->RevokeFriendshipRequest($a, $b));
        $this->assertFalse(
            $relationshipService->RevokeFriendshipRequest($a, $b));

        $this->assertFalse($a->userRelationship()->isFriend($b));
        $this->assertTrue($a->userRelationship()->canSendFriendshipRequest($b));
        $this->assertTrue($b->userRelationship()->canSendFriendshipRequest($a));
    }

    public function testAcceptRelationshipRequest()
    {
        $em = $this->createEm();
        $relationshipService = $this->getRelationshipService($em);

        $em->persist($a = new User());
        $em->persist($b = new User());

        $relationshipService->RequestFriendship($b, $a);
        $em->flush();

        $this->assertTrue(
            $relationshipService->AcceptFriendshipRequest($a, $b));
        $this->assertFalse(
            $relationshipService->AcceptFriendshipRequest($a, $b));

        $this->assertTrue($a->userRelationship()->isFriend($b));
    }

    public function testRejectRelationshipRequest()
    {
        $em = $this->createEm();
        $relationshipService = $this->getRelationshipService($em);

        $em->persist($a = new User());
        $em->persist($b = new User());

        $relationshipService->RequestFriendship($b, $a);
        $em->flush();

        $this->assertTrue(
            $relationshipService->RejectFriendshipRequest($a, $b));
        $this->assertFalse(
            $relationshipService->RejectFriendshipRequest($a, $b));

        $this->assertFalse($a->userRelationship()->isFriend($b));
        $this->assertTrue($a->userRelationship()->canSendFriendshipRequest($b));
        $this->assertTrue($b->userRelationship()->canSendFriendshipRequest($a));
    }

    public function testTerminateRelationship()
    {
        $em = $this->createEm();
        $relationshipService = $this->getRelationshipService($em);

        $em->persist($a = new User());
        $em->persist($b = new User());

        $relationshipService->RequestFriendship($a, $b);
        $em->flush();

        $relationshipService->AcceptFriendshipRequest($b, $a);
        $em->flush();

        $this->assertTrue($a->userRelationship()->isFriend($b));
        $this->assertTrue($b->userRelationship()->isFriend($a));

        $this->assertTrue(
            $relationshipService->TerminateFriendship($a, $b));
        $this->assertFalse(
            $relationshipService->TerminateFriendship($a, $b));
        $em->flush();

        $this->assertFalse($a->userRelationship()->isFriend($b));
        $this->assertFalse($b->userRelationship()->isFriend($a));

    }

}
