<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampCollaborationTest extends AbstractTestCase {
    public function testCampCollaboration() {
        $camp = new Camp();
        $user = new User();

        $collaboration = new CampCollaboration();
        $collaboration->setCamp($camp);
        $collaboration->setUser($user);
        $collaboration->setRole(CampCollaboration::ROLE_MEMBER);
        $collaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
        $collaboration->setCollaborationAcceptedBy('install');

        $this->assertEquals($camp, $collaboration->getCamp());
        $this->assertEquals($user, $collaboration->getUser());
        $this->assertEquals(CampCollaboration::ROLE_MEMBER, $collaboration->getRole());
        $this->assertEquals(CampCollaboration::STATUS_ESTABLISHED, $collaboration->getStatus());
        $this->assertEquals('install', $collaboration->getCollaborationAcceptedBy());
    }

    public function testStatus() {
        $collaboration = new CampCollaboration();
        $collaboration->setStatus(CampCollaboration::STATUS_REQUESTED);
        $this->assertTrue($collaboration->isRequest());

        $collaboration->setStatus(CampCollaboration::STATUS_INVITED);
        $this->assertTrue($collaboration->isInvitation());

        $collaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
        $this->assertTrue($collaboration->isEstablished());

        $this->expectException('Exception');
        $collaboration->setStatus('test');
    }

    public function testRole() {
        $collaboration = new CampCollaboration();
        $collaboration->setRole(CampCollaboration::ROLE_GUEST);
        $this->assertTrue($collaboration->isGuest());

        $collaboration->setRole(CampCollaboration::ROLE_MEMBER);
        $this->assertTrue($collaboration->isMember());

        $collaboration->setRole(CampCollaboration::ROLE_MANAGER);
        $this->assertTrue($collaboration->isManager());

        $this->expectException('Exception');
        $collaboration->setRole('test');
    }

    public function testLifecycle() {
        $collaboration = new CampCollaboration();
        $collaboration->setStatus(CampCollaboration::STATUS_REQUESTED);
        $collaboration->setCollaborationAcceptedBy('install');

        $collaboration->PrePersist();
        $collaboration->PreUpdate();

        $this->assertNull($collaboration->getCollaborationAcceptedBy());
    }
}
