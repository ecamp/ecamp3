<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Patch;
use App\DTO\Invitation;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Repository\CampCollaborationRepository;
use App\State\InvitationAcceptProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @internal
 */
class InvitationAcceptProcessorTest extends TestCase {
    public const INVITEKEY = 'inviteKey';
    public const INVITEKEYHASH = 'sl3hC12VkIUzT89mMggYyoMmFuo=';

    private Invitation $invitation;
    private CampCollaboration $campCollaboration;
    private User $user;

    private MockObject|CampCollaborationRepository $collaborationRepository;
    private MockObject|Security $security;
    private MockObject|PasswordHasherFactoryInterface $pwHasherFactory;
    private MockObject|EntityManagerInterface $em;

    private InvitationAcceptProcessor $processor;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->invitation = new Invitation(self::INVITEKEY, '', '', '', false);
        $this->campCollaboration = new CampCollaboration();
        $this->user = new User();

        $this->collaborationRepository = $this->createMock(CampCollaborationRepository::class);
        $this->security = $this->createMock(Security::class);
        $this->pwHasherFactory = $this->createMock(PasswordHasherFactory::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->processor = new InvitationAcceptProcessor(
            $this->pwHasherFactory,
            $this->collaborationRepository,
            $this->security,
            $this->em
        );
    }

    public function testUpdatesInvitationCorrectlyOnAccept() {
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
        $this->security->expects(self::once())->method('getUser')->willReturn($this->user);
        $this->em->expects($this->exactly(1))->method('flush');

        $this->processor->process($this->invitation, new Patch());

        self::assertThat($this->campCollaboration->user, self::equalTo($this->user));
        self::assertThat($this->campCollaboration->status, self::equalTo(CampCollaboration::STATUS_ESTABLISHED));
        self::assertThat($this->campCollaboration->inviteKey, self::isNull());
        self::assertThat($this->campCollaboration->inviteEmail, self::isNull());
    }

    // public function testUpdatesInvitationCorrectlyOnReject() {
    //     /** @var MockObject|PasswordHasherInterface $pwHasher */
    //     $pwHasher = $this->createMock(PasswordHasherInterface::class);
    //     $pwHasher->expects(self::once())
    //         ->method('hash')
    //         ->with(self::INVITEKEY)
    //         ->willReturn(self::INVITEKEYHASH)
    //     ;
    //     $this->pwHasherFactory
    //         ->expects(self::once())
    //         ->method('getPasswordHasher')
    //         ->with('MailToken')
    //         ->willReturn($pwHasher)
    //     ;
    //     $this->collaborationRepository
    //         ->expects(self::once())
    //         ->method('findByInviteKeyHash')
    //         ->with(self::INVITEKEYHASH)
    //         ->willReturn($this->campCollaboration)
    //     ;
    //     $this->security->expects(self::never())->method('getUser');

    //     $result = $this->processor->onReject($this->invitation);

    //     self::assertThat($result->status, self::equalTo(CampCollaboration::STATUS_INACTIVE));
    //     self::assertThat($result->inviteKey, self::isNull());
    //     self::assertThat($result->inviteEmail, self::equalTo($this->campCollaboration->inviteEmail));
    //     self::assertThat($result->user, self::equalTo($this->campCollaboration->user));
    // }
}
