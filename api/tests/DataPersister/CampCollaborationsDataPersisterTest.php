<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\CampCollaborationDataPersister;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

/**
 * @internal
 */
class CampCollaborationsDataPersisterTest extends TestCase {
    private CampCollaborationDataPersister $dataPersister;
    private MockObject|ContextAwareDataPersisterInterface $decoratedMock;
    private Security|MockObject $security;
    private UserRepository|MockObject $userRepository;
    private MockObject|MailService $mailService;
    private CampCollaboration $campCollaboration;
    private $user;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->mailService = $this->createMock(MailService::class);

        $this->campCollaboration = new CampCollaboration();
        $this->campCollaboration->status = CampCollaboration::STATUS_INVITED;
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->campCollaboration->camp = new Camp();

        $this->user = new User();

        $this->dataPersister = new CampCollaborationDataPersister($this->decoratedMock, $this->security, $this->userRepository, $this->mailService);
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects(self::exactly(2))
            ->method('supports')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->dataPersister->supports($this->campCollaboration, []));
        $this->assertFalse($this->dataPersister->supports($this->campCollaboration, []));
    }

    public function testDoesNotSupportNonCamp() {
        $this->decoratedMock
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testDelegatesPersistToDecorated() {
        // given
        $this->decoratedMock->expects(self::once())
            ->method('persist')
        ;

        // when
        $this->dataPersister->persist($this->campCollaboration, []);
    }

    public function testDelegatesRemove() {
        $this->decoratedMock->expects(self::once())->method('remove');

        $this->dataPersister->remove([], []);
    }

    public function testSendsEmailWhenStatusInvited() {
        $this->security->method('getUser')->willReturn($this->user);

        $this->mailService->expects(self::once())
            ->method('sendInviteToCampMail')
        ;

        $this->dataPersister->persist($this->campCollaboration, [
            'collection_operation_name' => 'post',
        ]);
    }

    public function testAttachesKnownUser() {
        $this->security->method('getUser')->willReturn($this->user);
        $this->userRepository->method('findOneBy')->willReturn($this->user);

        $this->mailService->expects(self::once())
            ->method('sendInviteToCampMail')
        ;

        $this->dataPersister->persist($this->campCollaboration, [
            'collection_operation_name' => 'post',
        ]);

        self::assertThat($this->campCollaboration->user, self::logicalNot(self::isNull()));
    }

    public function testNotSendsEmailWhenStatusNotInvited() {
        $this->campCollaboration->status = CampCollaboration::STATUS_INACTIVE;
        $this->security->method('getUser')->willReturn($this->user);

        $this->mailService->expects(self::never())
            ->method('sendInviteToCampMail')
        ;

        $this->dataPersister->persist($this->campCollaboration, [
            'collection_operation_name' => 'post',
        ]);
    }

    public function testNotSendsEmailWhenNoInviteEmail() {
        $this->campCollaboration->inviteEmail = null;
        $this->security->method('getUser')->willReturn($this->user);

        $this->mailService->expects(self::never())
            ->method('sendInviteToCampMail')
        ;

        $this->dataPersister->persist($this->campCollaboration, [
            'collection_operation_name' => 'post',
        ]);
    }
}
