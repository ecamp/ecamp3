<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Patch;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\State\InvitationRejectProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @internal
 */
class InvitationRejectProcessorTest extends TestCase {
    public const INVITEKEY = 'inviteKey';
    public const INVITEKEYHASH = 'sl3hC12VkIUzT89mMggYyoMmFuo=';

    private Invitation $invitation;
    private CampCollaboration $campCollaboration;
    private User $user;

    private MockObject|CampCollaborationRepository $collaborationRepository;
    private MockObject|PasswordHasherFactoryInterface $pwHasherFactory;
    private MockObject|EntityManagerInterface $em;

    private InvitationRejectProcessor $processor;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->invitation = new Invitation(self::INVITEKEY, '', '', '', false);
        $this->campCollaboration = new CampCollaboration();
        $this->user = new User();

        $this->collaborationRepository = $this->createMock(CampCollaborationRepository::class);
        $this->pwHasherFactory = $this->createMock(PasswordHasherFactory::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->processor = new InvitationRejectProcessor(
            $this->pwHasherFactory,
            $this->collaborationRepository,
            $this->em
        );
    }

    public function testUpdatesInvitationCorrectlyOnReject() {
        /** @var MockObject|PasswordHasherInterface $pwHasher */
        $pwHasher = $this->createMock(PasswordHasherInterface::class);
        $pwHasher->expects(self::once())
            ->method('hash')
            ->with(self::INVITEKEY)
            ->willReturn(self::INVITEKEYHASH)
        ;
        $this->pwHasherFactory
            ->expects(self::once())
            ->method('getPasswordHasher')
            ->with('MailToken')
            ->willReturn($pwHasher)
        ;
        $this->collaborationRepository
            ->expects(self::once())
            ->method('findByInviteKeyHash')
            ->with(self::INVITEKEYHASH)
            ->willReturn($this->campCollaboration)
        ;
        $this->em->expects($this->exactly(1))->method('flush');

        $this->processor->process($this->invitation, new Patch());

        self::assertThat($this->campCollaboration->status, self::equalTo(CampCollaboration::STATUS_INACTIVE));
        self::assertThat($this->campCollaboration->inviteKey, self::isNull());
        self::assertThat($this->campCollaboration->inviteEmail, self::equalTo($this->campCollaboration->inviteEmail));
        self::assertThat($this->campCollaboration->user, self::equalTo($this->campCollaboration->user));
    }
}
