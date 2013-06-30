<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCoreTest\Mock\AclMock;
use EcampCoreTest\Mock\EntityManagerMock;
use EcampCoreTest\Bootstrap;
use Doctrine\ORM\EntityManager;
use EcampCore\Service\CampCollaborationService;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampType;
use EcampCore\Entity\CampCollaboration;

class CampCollaborationServiceTest extends \PHPUnit_Framework_TestCase
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
     * @return \EcampCore\Service\CampCollaborationService
     */
    private function getCampCollaborationService(EntityManager $em)
    {
        $ccRepo = $em->getRepository('EcampCore\Entity\CampCollaboration');

        $campCollaborationService =  new CampCollaborationService($ccRepo);
        $campCollaborationService->setEntityManager($em);
        $campCollaborationService->setAcl($this->getAcl());

        return $campCollaborationService;
    }

    public function testRequestCollaboration()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->requestCollaboration($user, $camp);

        $this->assertTrue($user->campCollaboration()->hasSentCollaborationRequestTo($camp));
        $this->assertTrue($camp->campCollaboration()->hasReceivedCollaborationRequestFrom($user));

        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertTrue($cc->isRequest());
        $this->assertEquals($user, $cc->getUser());
        $this->assertEquals($camp, $cc->getCamp());

        $this->assertContains($cc, $user->campCollaboration()->getSentCollaborationRequests(CampCollaboration::ROLE_GUEST));
        $this->assertContains($cc, $camp->campCollaboration()->getReceivedCollaborationRequests(CampCollaboration::ROLE_GUEST));
    }

    public function testRevokeRequest()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->requestCollaboration($user, $camp, CampCollaboration::ROLE_MANAGER);
        $em->flush();

        $campCollaborationService->revokeRequest($user, $camp);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertNull($cc);

        $this->assertTrue($user->campCollaboration()->canSendCollaborationRequest($camp));
        $this->assertTrue($camp->campCollaboration()->canSendCollaborationInvitation($user));
    }

    public function testAcceptRequest()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->requestCollaboration($user, $camp, CampCollaboration::ROLE_MANAGER);
        $em->flush();

        $campCollaborationService->acceptRequest($manager, $camp, $user, CampCollaboration::ROLE_MEMBER);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertTrue($cc->isEstablished());
        $this->assertFalse($user->campCollaboration()->hasSentCollaborationRequestTo($camp));
        $this->assertFalse($camp->campCollaboration()->hasReceivedCollaborationRequestFrom($user));

        $this->assertTrue($user->campCollaboration()->isCollaboratorOf($camp));
        $this->assertTrue($camp->campCollaboration()->hasCollaborator($user));

        $this->assertEquals($cc, $user->campCollaboration()->getCollaboration($camp));
        $this->assertEquals($cc, $camp->campCollaboration()->getCollaboration($user));
    }

    public function testRejectRequest()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->requestCollaboration($user, $camp, CampCollaboration::ROLE_MANAGER);
        $em->flush();

        $campCollaborationService->rejectRequest($camp, $user);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertNull($cc);

        $this->assertTrue($user->campCollaboration()->canSendCollaborationRequest($camp));
        $this->assertTrue($camp->campCollaboration()->canSendCollaborationInvitation($user));
    }

    public function testInviteUser()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->inviteUser($manager, $camp, $user, CampCollaboration::ROLE_MANAGER);

        $this->assertTrue($user->campCollaboration()->hasReceivedCollaborationInvitationFrom($camp));
        $this->assertTrue($camp->campCollaboration()->hasSentCollaborationInvitationTo($user));

        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertTrue($cc->isInvitation());
        $this->assertEquals(CampCollaboration::ROLE_MANAGER, $cc->getRole());
        $this->assertEquals($user, $cc->getUser());
        $this->assertEquals($camp, $cc->getCamp());

        $this->assertContains($cc, $user->campCollaboration()->getReceivedCollaborationInvitations(CampCollaboration::ROLE_MANAGER));
        $this->assertContains($cc, $camp->campCollaboration()->getSentCollaborationInvitations(CampCollaboration::ROLE_MANAGER));
    }

    public function testRevokeInvitation()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->inviteUser($manager, $camp, $user, CampCollaboration::ROLE_MANAGER);
        $em->flush();

        $campCollaborationService->revokeInvitation($camp, $user);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertNull($cc);

        $this->assertTrue($user->campCollaboration()->canSendCollaborationRequest($camp));
        $this->assertTrue($camp->campCollaboration()->canSendCollaborationInvitation($user));
    }

    public function testAcceptInvitation()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->inviteUser($manager, $camp, $user, CampCollaboration::ROLE_MANAGER);
        $em->flush();

        $campCollaborationService->acceptInvitation($user, $camp);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertTrue($cc->isEstablished());
        $this->assertFalse($user->campCollaboration()->hasSentCollaborationRequestTo($camp));
        $this->assertFalse($camp->campCollaboration()->hasReceivedCollaborationRequestFrom($user));

        $this->assertTrue($user->campCollaboration()->isCollaboratorOf($camp));
        $this->assertTrue($camp->campCollaboration()->hasCollaborator($user));

        $this->assertEquals($cc, $user->campCollaboration()->getCollaboration($camp));
        $this->assertEquals($cc, $camp->campCollaboration()->getCollaboration($user));
    }

    public function testRejectInvitation()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->inviteUser($manager, $camp, $user, CampCollaboration::ROLE_MANAGER);
        $em->flush();

        $campCollaborationService->rejectInvitation($user, $camp);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertNull($cc);

        $this->assertTrue($user->campCollaboration()->canSendCollaborationRequest($camp));
        $this->assertTrue($camp->campCollaboration()->canSendCollaborationInvitation($user));
    }

    public function testLeaveGroup()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->requestCollaboration($user, $camp);
        $em->flush();
        $campCollaborationService->acceptRequest($manager, $camp, $user);
        $em->flush();

        $campCollaborationService->leaveCamp($user, $camp);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertNull($cc);

        $this->assertTrue($user->campCollaboration()->canSendCollaborationRequest($camp));
        $this->assertTrue($camp->campCollaboration()->canSendCollaborationInvitation($user));
    }

    public function testKickUser()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $em->persist($manager = new User());
        $em->persist($user = new User());
        $em->persist($campType = new CampType('name', 'type'));
        $em->persist($camp = new Camp($campType));
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);

        $campCollaborationService->requestCollaboration($user, $camp);
        $em->flush();
        $campCollaborationService->acceptRequest($manager, $camp, $user);
        $em->flush();

        $campCollaborationService->kickUser($camp, $user);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertNull($cc);

        $this->assertTrue($user->campCollaboration()->canSendCollaborationRequest($camp));
        $this->assertTrue($camp->campCollaboration()->canSendCollaborationInvitation($user));
    }
}
