<?php

namespace App\Tests\Security\Voter;

use App\Entity\Activity;
use App\Entity\BaseEntity;
use App\Entity\BelongsToContentNodeTreeInterface;
use App\Entity\Camp;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\Period;
use App\Entity\User;
use App\HttpCache\AccessTagger;
use App\Security\Voter\CampIsPublicVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @internal
 */
class CampIsPublicVoterTest extends TestCase {
    private CampIsPublicVoter $voter;
    private MockObject|TokenInterface $token;
    private EntityManagerInterface|MockObject $em;
    private AccessTagger|MockObject $accessTagger;

    public function setUp(): void {
        parent::setUp();
        $this->token = $this->createStub(TokenInterface::class);
        $this->em = $this->createStub(EntityManagerInterface::class);
        $this->accessTagger = $this->createStub(AccessTagger::class);
        $this->voter = new CampIsPublicVoter($this->em, $this->accessTagger);
    }

    public function testDoesntVoteWhenAttributeWrong() {
        // given

        // when
        $result = $this->voter->vote($this->token, new Period(), ['CAMP_IS_SOMETHING_ELSE']);

        // then
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDoesntVoteWhenSubjectDoesNotBelongToCamp() {
        // given

        // when
        $result = $this->voter->vote($this->token, new CampIsPublicVoterTestDummy(), ['CAMP_IS_PUBLIC']);

        // then
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDoesntVoteWhenSubjectIsNull() {
        // given

        // when
        $result = $this->voter->vote($this->token, null, ['CAMP_IS_PUBLIC']);

        // then
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDeniesAccessWhenGetCampYieldsNull() {
        // given
        $this->token->method('getUser')->willReturn(new User());
        $subject = $this->createStub(Period::class);
        $subject->method('getCamp')->willReturn(null);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_IS_PUBLIC']);

        // then
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessWhenCampIsntPublic() {
        // given
        $user = $this->createStub(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $camp = new Camp();
        $camp->isPublic = false;
        $subject = $this->createStub(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_IS_PUBLIC']);

        // then
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testGrantsAccessViaBelongsToCampInterface() {
        // given
        $user = $this->createStub(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $camp = new Camp();
        $camp->isPublic = true;
        $subject = $this->createStub(Period::class);
        $subject->method('getCamp')->willReturn($camp);

        $this->accessTagger = $this->createMock(AccessTagger::class);
        $this->accessTagger->expects($this->once())->method('addTags')->with([$camp->getId()]);
        $this->voter = new CampIsPublicVoter($this->em, $this->accessTagger);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_IS_PUBLIC']);

        // then
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testGrantsAccessViaBelongsToContentNodeTreeInterface() {
        // given
        $user = $this->createStub(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $this->token->method('getUser')->willReturn($user);
        $camp = new Camp();
        $camp->isPublic = true;
        $activity = $this->createStub(Activity::class);
        $activity->method('getCamp')->willReturn($camp);
        $root = $this->createStub(ColumnLayout::class);
        $subject = $this->createStub(ContentNodeTreeDummy3::class);
        $subject->method('getRoot')->willReturn($root);
        $repository = $this->createStub(EntityRepository::class);
        $this->em->method('getRepository')->willReturn($repository);
        $repository->method('findOneBy')->willReturn($activity);

        // when
        $result = $this->voter->vote($this->token, $subject, ['CAMP_IS_PUBLIC']);

        // then
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }
}

class CampIsPublicVoterTestDummy extends BaseEntity {}

class ContentNodeTreeDummy3 implements BelongsToContentNodeTreeInterface {
    public function getRoot(): ?ColumnLayout {
        return null;
    }
}
