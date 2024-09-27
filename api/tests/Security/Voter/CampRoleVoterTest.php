<?php

namespace App\Tests\Security\Voter;

use App\Entity\Activity;
use App\Entity\BaseEntity;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\Period;
use App\Entity\User;
use App\HttpCache\ResponseTagger;
use App\Security\Voter\CampRoleVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @internal
 */
class CampRoleVoterTest extends TestCase {
    private CampRoleVoter $voter;
    private MockObject|TokenInterface $token;
    private EntityManagerInterface|MockObject $em;
    private MockObject|ResponseTagger $responseTagger;

    public function setUp(): void {
        parent::setUp();
        $this->token = $this->createMock(TokenInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->responseTagger = $this->createMock(ResponseTagger::class);
        $this->voter = new CampRoleVoter($this->em, $this->responseTagger);
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

    public function testDeniesAccessWhenGetCampYieldsNull() {
        // given
        $this->token->method('getUser')->willReturn(new User());
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn(null);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
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

    public function testGrantsAccessViaBelongsToCampInterface() {
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

        $this->responseTagger->expects($this->once())->method('addTags')->with([$collaboration->getId()]);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testGrantsAccessViaBelongsToContentNodeTreeInterface() {
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
        $activity = $this->createMock(Activity::class);
        $activity->method('getCamp')->willReturn($camp);
        $subject = $this->createMock(ColumnLayout::class);
        $subject->method('getRoot')->willReturn($subject);
        $repository = $this->createMock(EntityRepository::class);
        $this->em->method('getRepository')->willReturn($repository);
        $repository->method('findOneBy')->willReturn($activity);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_COLLABORATOR']);

        // then
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }
}

class CampRoleVoterTestDummy extends BaseEntity {}
