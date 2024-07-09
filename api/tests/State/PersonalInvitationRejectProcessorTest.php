<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Patch;
use App\DTO\PersonalInvitation;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\Repository\UserRepository;
use App\State\PersonalInvitationRejectProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @internal
 */
class PersonalInvitationRejectProcessorTest extends TestCase {
    public const ID = '1a2b3c4d';

    private PersonalInvitation $invitation;
    private CampCollaboration $campCollaboration;
    private User $user;

    private CampCollaborationRepository|MockObject $collaborationRepository;
    private MockObject|UserRepository $userRepository;
    private MockObject|Security $security;
    private EntityManagerInterface|MockObject $em;

    private PersonalInvitationRejectProcessor $processor;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->invitation = new PersonalInvitation(self::ID, '', '', '', false);
        $this->campCollaboration = new CampCollaboration();
        $this->user = $this->createMock(User::class);

        $this->collaborationRepository = $this->createMock(CampCollaborationRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->security = $this->createMock(Security::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->processor = new PersonalInvitationRejectProcessor(
            $this->collaborationRepository,
            $this->userRepository,
            $this->security,
            $this->em
        );
    }

    public function testUpdatesPersonalInvitationCorrectlyOnReject() {
        $this->user
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->willReturn('9876abcd')
        ;
        $this->collaborationRepository
            ->expects(self::once())
            ->method('findByUserAndIdAndInvited')
            ->with($this->user, self::ID)
            ->willReturn($this->campCollaboration)
        ;
        $this->userRepository
            ->expects(self::once())
            ->method('loadUserByIdentifier')
            ->with('9876abcd')
            ->willReturn($this->user)
        ;
        $this->security->expects(self::once())->method('getUser')->willReturn($this->user);
        $this->em->expects($this->exactly(1))->method('flush');

        $this->processor->process($this->invitation, new Patch());

        self::assertThat($this->campCollaboration->status, self::equalTo(CampCollaboration::STATUS_INACTIVE));
        self::assertThat($this->campCollaboration->inviteKey, self::isNull());
        self::assertThat($this->campCollaboration->inviteEmail, self::equalTo($this->campCollaboration->inviteEmail));
        self::assertThat($this->campCollaboration->user, self::equalTo($this->campCollaboration->user));
    }
}
