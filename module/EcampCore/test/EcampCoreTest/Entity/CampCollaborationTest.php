<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampType;
use EcampCore\Entity\CampCollaboration;

class CampCollaborationTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateRequest()
    {
        $campType = new CampType('name', 'type');
        $camp = new Camp($campType);
        $user = new User();

        $campCollaboration = CampCollaboration::createRequest($user, $camp);

        $this->assertEquals($user, $campCollaboration->getUser());
        $this->assertEquals($camp, $campCollaboration->getCamp());
        $this->assertEquals(CampCollaboration::ROLE_GUEST, $campCollaboration->getRole());
        $this->assertEquals(CampCollaboration::STATUS_REQUESTED, $campCollaboration->getStatus());

        $this->assertTrue($campCollaboration->isGuest());
        $this->assertFalse($campCollaboration->isMember());
        $this->assertFalse($campCollaboration->isManager());
        $this->assertFalse($campCollaboration->isOwner());

        $this->assertTrue($campCollaboration->isRequest());
        $this->assertFalse($campCollaboration->isInvitation());
        $this->assertFalse($campCollaboration->isEstablished());

        $this->assertTrue($user->campCollaboration()->hasSentCollaborationRequestTo($camp));
        $this->assertTrue($camp->campCollaboration()->hasReceivedCollaborationRequestFrom($user));

        $this->assertContains($campCollaboration, $user->campCollaboration()->getSentCollaborationRequests());
        $this->assertContains($campCollaboration, $camp->campCollaboration()->getReceivedCollaborationRequests());
    }

    public function testCreateInvitation()
    {
        $campType = new CampType('name', 'type');
        $camp = new Camp($campType);
        $manager = new User();
        $user = new User();

        $campCollaboration = CampCollaboration::createInvitation($user, $camp, $manager);

        $this->assertEquals($user, $campCollaboration->getUser());
        $this->assertEquals($camp, $campCollaboration->getCamp());
        $this->assertEquals(CampCollaboration::ROLE_GUEST, $campCollaboration->getRole());
        $this->assertEquals(CampCollaboration::STATUS_INVITED, $campCollaboration->getStatus());

        $this->assertTrue($campCollaboration->isGuest());
        $this->assertFalse($campCollaboration->isMember());
        $this->assertFalse($campCollaboration->isManager());
        $this->assertFalse($campCollaboration->isOwner());

        $this->assertFalse($campCollaboration->isRequest());
        $this->assertTrue($campCollaboration->isInvitation());
        $this->assertFalse($campCollaboration->isEstablished());

        $this->assertTrue($user->campCollaboration()->hasReceivedCollaborationInvitationFrom($camp));
        $this->assertTrue($camp->campCollaboration()->hasSentCollaborationInvitationTo($user));

        $this->assertContains($campCollaboration, $user->campCollaboration()->getReceivedCollaborationInvitations());
        $this->assertContains($campCollaboration, $camp->campCollaboration()->getSentCollaborationInvitations());
    }

    public function testAcceptRequest()
    {
        $campType = new CampType('name', 'type');
        $camp = new Camp($campType);
        $manager = new User();
        $user = new User();

        $campCollaboration = CampCollaboration::createRequest($user, $camp);
        $campCollaboration->acceptRequest($manager);

        $this->assertFalse($campCollaboration->isRequest());
        $this->assertFalse($campCollaboration->isInvitation());
        $this->assertTrue($campCollaboration->isEstablished());

    }

    public function testAcceptInvitation()
    {
        $campType = new CampType('name', 'type');
        $camp = new Camp($campType);
        $manager = new User();
        $user = new User();

        $campCollaboration = CampCollaboration::createInvitation($user, $camp, $manager);
        $campCollaboration->acceptInvitation();

        $this->assertFalse($campCollaboration->isRequest());
        $this->assertFalse($campCollaboration->isInvitation());
        $this->assertTrue($campCollaboration->isEstablished());

    }
}
