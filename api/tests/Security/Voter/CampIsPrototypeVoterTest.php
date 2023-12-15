<?php

namespace App\Tests\Security\Voter;

use App\Entity\Activity;
use App\Entity\BaseEntity;
use App\Entity\Camp;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\Period;
use App\Entity\User;
use App\Security\Voter\CampIsPrototypeVoter;
use App\Security\Voter\CampRoleVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @internal
 */
class CampIsPrototypeVoterTest extends TestCase {
    private CampIsPrototypeVoter $voter;
    private MockObject|TokenInterface $token;
    private EntityManagerInterface|MockObject $em;

    public function setUp(): void {
        parent::setUp();
        $this->token = $this->createMock(TokenInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->voter = new CampIsPrototypeVoter($this->em);
    }

    public function testDoesntVoteWhenAttributeWrong() {
        // given

        // when
        $result = $this->voter->vote($this->token, new Period(), ['CAMP_IS_SOMETHING_ELSE']);

        // then
        $this->assertEquals(CampRoleVoter::ACCESS_ABSTAIN, $result);
    }

    public function testDoesntVoteWhenSubjectDoesNotBelongToCamp() {
        // given

        // when
        $result = $this->voter->vote($this->token, new CampIsPrototypeVoterTestDummy(), ['CAMP_IS_PROTOTYPE']);

        // then
        $this->assertEquals(CampRoleVoter::ACCESS_ABSTAIN, $result);
    }

    public function testDoesntVoteWhenSubjectIsNull() {
        // given

        // when
        $result = $this->voter->vote($this->token, null, ['CAMP_IS_PROTOTYPE']);

        // then
        $this->assertEquals(CampRoleVoter::ACCESS_ABSTAIN, $result);
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
        $result = $this->voter->vote($this->token, $subject, ['CAMP_IS_PROTOTYPE']);

        // then
        $this->assertEquals(CampRoleVoter::ACCESS_GRANTED, $result);
    }

    public function testDeniesAccessWhenCampIsntPrototype() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $camp = new Camp();
        $camp->isPrototype = false;
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_IS_PROTOTYPE']);

        // then
        $this->assertEquals(CampRoleVoter::ACCESS_DENIED, $result);
    }

    public function testGrantsAccessViaBelongsToCampInterface() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $camp = new Camp();
        $camp->isPrototype = true;
        $subject = $this->createMock(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_IS_PROTOTYPE']);

        // then
        $this->assertEquals(CampRoleVoter::ACCESS_GRANTED, $result);
    }

    public function testGrantsAccessViaBelongsToContentNodeTreeInterface() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $camp = new Camp();
        $camp->isPrototype = true;
        $activity = $this->createMock(Activity::class);
        $activity->method('getCamp')->willReturn($camp);
        $subject = $this->createMock(ColumnLayout::class);
        $subject->method('getRoot')->willReturn($subject);
        $repository = $this->createMock(EntityRepository::class);
        $this->em->method('getRepository')->willReturn($repository);
        $repository->method('findOneBy')->willReturn($activity);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_IS_PROTOTYPE']);

        // then
        $this->assertEquals(CampRoleVoter::ACCESS_GRANTED, $result);
    }
}

class CampIsPrototypeVoterTestDummy extends BaseEntity {}
