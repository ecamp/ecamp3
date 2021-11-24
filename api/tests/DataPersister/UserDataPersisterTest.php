<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\UserDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\User;
use App\Service\MailService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 */
class UserDataPersisterTest extends TestCase {
    private UserDataPersister $dataPersister;
    private MockObject|ContextAwareDataPersisterInterface $decoratedMock;
    private MockObject|UserPasswordHasherInterface $userPasswordHasher;
    private MockObject|MailService $mailService;
    private User $user;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->userPasswordHasher = $this->createMock(UserPasswordHasher::class);
        $this->mailService = $this->createMock(MailService::class);
        $this->user = new User();

        $requestStack = RequestStackMockFactory::create(fn (string $className) => $this->createMock($className));
        $dataPersisterObservable = new DataPersisterObservable($this->decoratedMock, $requestStack);

        $this->dataPersister = new UserDataPersister($dataPersisterObservable, $this->userPasswordHasher, $this->mailService);
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->dataPersister->supports($this->user, []));
        $this->assertFalse($this->dataPersister->supports($this->user, []));
    }

    public function testDoesNotSupportNonUser() {
        $this->decoratedMock
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testDelegatesPersistToDecorated() {
        // given
        $this->decoratedMock->expects($this->once())
            ->method('persist')
            ->willReturnArgument(0)
        ;

        // when
        $this->dataPersister->persist($this->user, []);

        // then
    }

    public function testDoesNothingNormally() {
        // given
        $this->userPasswordHasher->expects($this->never())->method('hashPassword');
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var User $result */
        $data = $this->dataPersister->persist($this->user, ['collection_operation_name' => 'post']);

        // then
        $this->assertNull($data->password);
        $this->assertNull($data->plainPassword);
    }

    public function testHashesPasswordWhenPlainPasswordIsSet() {
        // given
        $this->user->plainPassword = 'test plain password';
        $this->userPasswordHasher->expects($this->once())->method('hashPassword')->willReturn('test hash');
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var User $result */
        $data = $this->dataPersister->persist($this->user, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals('test hash', $data->password);
        $this->assertNull($data->plainPassword);
    }

    public function testCreateAndSendActivationKey() {
        // given
        $this->mailService->expects($this->once())->method('sendUserActivationMail');
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var User $data */
        $data = $this->dataPersister->persist($this->user, ['collection_operation_name' => 'post']);

        // then
        $this->assertNotNull($data->activationKeyHash);
    }
}
