<?php

namespace EcampCoreTest\Entity;

use Bootstrap;
use EcampCore\Entity\User;
use EcampCoreTest\Mock\AclMock;
use EcampCoreTest\Mock\EntityManagerMock;
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

    private function openRequest()
    {
        $em = $this->createEm();
        $campCollaborationService = $this->getCampCollaborationService($em);

        $user = new User();
        $campType = new CampType('name', true, CampType::ORGANIZATION_PBS, true);
        $camp = new Camp();
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setCreator($user);
        $camp->setOwner($user);
        $camp->setCampType($campType);

        $em->persist($user);
        $em->persist($campType);
        $em->persist($camp);

        $campCollaborationService->requestCollaboration($user, $camp);
        $em->flush();

        $arg['em'] = $em;
        $arg['user'] = $user;
        $arg['camp'] = $camp;
        $arg['campCollaborationService'] = $campCollaborationService;

        return $arg;
    }

    public function testRequestCollaboration()
    {
        $arg = $this->openRequest();
        $em = $arg['em'];
        $user = $arg['user'];
        $camp = $arg['camp'];
        $campCollaborationService = $arg['campCollaborationService'];

        $this->assertTrue($user->campCollaboration()->hasSentCollaborationRequestTo($camp));
        $this->assertTrue($camp->campCollaboration()->hasReceivedCollaborationRequestFrom($user));

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertTrue($cc->isRequest());
        $this->assertEquals($user, $cc->getUser());
        $this->assertEquals($camp, $cc->getCamp());

        $this->assertContains($cc, $user->campCollaboration()->getSentCollaborationRequests(CampCollaboration::ROLE_GUEST));
        $this->assertContains($cc, $camp->campCollaboration()->getReceivedCollaborationRequests(CampCollaboration::ROLE_GUEST));
    }

    public function testRevokeRequest()
    {
        $arg = $this->openRequest();
        $em = $arg['em'];
        $user = $arg['user'];
        $camp = $arg['camp'];
        $campCollaborationService = $arg['campCollaborationService'];

        $campCollaborationService->revokeRequest($user, $camp);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertNull($cc);

        $this->assertTrue($user->campCollaboration()->canSendCollaborationRequest($camp));
        $this->assertTrue($camp->campCollaboration()->canSendCollaborationInvitation($user));
    }

    public function testAcceptRequest()
    {
        $arg = $this->openRequest();
        $em = $arg['em'];
        $user = $arg['user'];
        $camp = $arg['camp'];
        $campCollaborationService = $arg['campCollaborationService'];

        $em->persist($manager = new User());

        $campCollaborationService->acceptRequest($manager, $camp, $user);
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
        $arg = $this->openRequest();
        $em = $arg['em'];
        $user = $arg['user'];
        $camp = $arg['camp'];
        $campCollaborationService = $arg['campCollaborationService'];

        $campCollaborationService->rejectRequest($camp, $user);
        $em->flush();

        $cc = $em->getRepository('EcampCore\Entity\CampCollaboration')->findByCampAndUser($camp, $user);

        $this->assertNull($cc);

        $this->assertTrue($user->campCollaboration()->canSendCollaborationRequest($camp));
        $this->assertTrue($camp->campCollaboration()->canSendCollaborationInvitation($user));
    }

    /*
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
    }*/
}
