<?php

namespace App\Tests\Security\Voter;

use App\Entity\BaseEntity;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\Period;
use App\Entity\User;
use App\Security\Voter\CampRoleVoter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @internal
 */
class CampRoleVoterTest extends TestCase {
    private CampRoleVoter $voter;
    private TokenInterface|MockObject $token;
    private MockObject|EntityManagerInterface $em;

    public function setUp(): void {
        parent::setUp();
        $this->token = $this->createMock(TokenInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->voter = new CampRoleVoter($this->em);
    }

    public function testDoesntVoteWhenAttributeWrong() {
        // given

        // when
        $result = $this->voter->vote($this->token, new Period(), ['CAMP_SUPPORTER']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDoesntVoteWhenSubjectDoesNotBelongToCamp() {
        // given

        // when
        $result = $this->voter->vote($this->token, new CampRoleVoterTestDummy(), ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDoesntVoteWhenSubjectIsNull() {
        // given

        // when
        $result = $this->voter->vote($this->token, null, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDeniesAccessWhenNotLoggedIn() {
        // given
        $this->token->method('getUser')->willReturn(null);

        // when
        $result = $this->voter->vote($this->token, new Period(), ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * When the camp associated with an entity is null, this isn't a security question,
     * but rather should have been caught by validation rules.
     * If the association with a camp really is optional for some entity, the security
     * rules can easily add a check manually:
     * is_granted("CAMP_COLLABORATOR", object) and null != object.getCamp().
     */
    public function testGrantsAccessWhenGetCampYieldsNull() {
        // given
        $this->token->method('getUser')->willReturn(new User());
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn(null);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testDeniesAccessWhenGetCampYieldsNullAndNotLoggedIn() {
        // given
        $this->token->method('getUser')->willReturn(null);
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn(null);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessWhenNoCampCollaborations() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $camp = new Camp();
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessWhenNoMatchingCampCollaboration() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $user2 = $this->createMock(User::class);
        $user2->method('getId')->willReturn('otherIdFromTest');
        $this->token->method('getUser')->willReturn($user);
        $collaboration = new CampCollaboration();
        $collaboration->user = $user2;
        $collaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $collaboration->role = CampCollaboration::ROLE_MANAGER;
        $camp = new Camp();
        $camp->collaborations->add($collaboration);
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessWhenMatchingCampCollaborationIsInvitation() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $user2 = $this->createMock(User::class);
        $user2->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $collaboration = new CampCollaboration();
        $collaboration->user = $user2;
        $collaboration->status = CampCollaboration::STATUS_INVITED;
        $collaboration->role = CampCollaboration::ROLE_MANAGER;
        $camp = new Camp();
        $camp->collaborations->add($collaboration);
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessWhenMatchingCampCollaborationIsInactive() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $user2 = $this->createMock(User::class);
        $user2->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $collaboration = new CampCollaboration();
        $collaboration->user = $user2;
        $collaboration->status = CampCollaboration::STATUS_INACTIVE;
        $collaboration->role = CampCollaboration::ROLE_MANAGER;
        $camp = new Camp();
        $camp->collaborations->add($collaboration);
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessWhenRolesDontMatch() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $user2 = $this->createMock(User::class);
        $user2->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $collaboration = new CampCollaboration();
        $collaboration->user = $user2;
        $collaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $collaboration->role = CampCollaboration::ROLE_GUEST;
        $camp = new Camp();
        $camp->collaborations->add($collaboration);
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_MANAGER']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testGrantsAccess() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $user2 = $this->createMock(User::class);
        $user2->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $collaboration = new CampCollaboration();
        $collaboration->user = $user2;
        $collaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $collaboration->role = CampCollaboration::ROLE_MANAGER;
        $camp = new Camp();
        $camp->collaborations->add($collaboration);
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }
}

class CampRoleVoterTestDummy extends BaseEntity {
}
