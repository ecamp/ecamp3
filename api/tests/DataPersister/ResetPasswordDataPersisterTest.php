<?php

namespace App\Tests\DataPersister;

use App\DataPersister\ResetPasswordDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\DTO\ResetPassword;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @internal
 */
class ResetPasswordDataPersisterTest extends TestCase {
    public const EMAIL = 'a@b.com';
    public const EMAILBASE64 = 'YUBiLmNvbQ==';

    private ResetPassword $resetPassword;

    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|UserRepository $userRepository;
    private MockObject|PasswordHasherFactoryInterface $pwHasherFactory;
    private MockObject|PasswordHasherInterface $pwHasher;
    private MockObject|MailService $mailService;
    private ResetPasswordDataPersister $dataPersister;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->resetPassword = new ResetPassword();

        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->pwHasherFactory = $this->createMock(PasswordHasherFactory::class);
        $this->pwHasher = $this->createMock(PasswordHasherInterface::class);
        $this->mailService = $this->createMock(MailService::class);

        $this->pwHasherFactory->expects(self::any())
            ->method('getPasswordHasher')
            ->willReturn($this->pwHasher)
        ;

        $this->dataPersister = new ResetPasswordDataPersister(
            $this->dataPersisterObservable,
            $this->entityManager,
            $this->userRepository,
            $this->pwHasherFactory,
            $this->mailService
        );
    }

    public function testSupportsResetPasswordAlsoIfDataPersisterObservableDoesNotSupportIt() {
        $this->dataPersisterObservable->expects(self::never())->method('supports')->willReturn(false);

        self::assertThat($this->dataPersister->supports($this->resetPassword), self::isTrue());
    }

    public function testDoesNotSupportAnythingElseThanResetPassword() {
        $this->dataPersisterObservable->expects(self::never())->method('supports')->willReturn(false);

        self::assertThat($this->dataPersister->supports(new stdClass()), self::isFalse());
        self::assertThat($this->dataPersister->supports(null), self::isFalse());
    }

    public function testRemoveIsNotSupported() {
        $this->expectException(Exception::class);

        $this->dataPersister->remove(null);
    }

    public function testCreateWithUnknownEmailDoesNotCreateResetKey() {
        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->willReturn(null)
        ;
        $this->mailService->expects(self::never())
            ->method('sendPasswordResetLink')
        ;

        $this->resetPassword->email = self::EMAIL;

        $data = $this->dataPersister->beforeCreate($this->resetPassword);
        self::assertThat($data->resetKey, self::isNull());
    }

    public function testCreateWithKnowneMailCreatesResetKey() {
        $user = new User();
        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->with(self::EMAIL)
            ->willReturn($user)
        ;

        $this->pwHasher->expects(self::once())
            ->method('hash')
            ->willReturnCallback(fn ($raw) => md5($raw))
        ;

        $this->mailService->expects(self::once())
            ->method('sendPasswordResetLink')
            ->with($user, $this->resetPassword)
        ;

        $this->resetPassword->email = self::EMAIL;
        $data = $this->dataPersister->beforeCreate($this->resetPassword);

        self::assertThat($data->emailBase64, self::equalTo(self::EMAILBASE64));
        self::assertThat($data->resetKey, self::logicalNot(self::isNull()));
    }

    public function testUpdateWithUnknownEmailThrowsException() {
        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->willReturn(null)
        ;

        $this->resetPassword->emailBase64 = self::EMAILBASE64;

        $this->expectException(Exception::class);
        $this->dataPersister->beforeUpdate($this->resetPassword);
    }

    public function testUpdateWithUserHasNoResetKeyThrowsException() {
        $user = new User();
        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->with(self::EMAIL)
            ->willReturn($user)
        ;

        $this->resetPassword->emailBase64 = self::EMAILBASE64;

        $this->expectException(Exception::class);
        $this->dataPersister->beforeUpdate($this->resetPassword);
    }

    public function testUpdateWithWrongResetKeyThrowsException() {
        $user = new User();
        $user->passwordResetKeyHash = 'resetKey';

        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->with(self::EMAIL)
            ->willReturn($user)
        ;
        $this->pwHasher->expects(self::once())
            ->method('verify')
            ->willReturn(false)
        ;

        $this->resetPassword->emailBase64 = self::EMAILBASE64;
        $this->resetPassword->resetKey = 'myKey';

        $this->expectException(Exception::class);
        $this->dataPersister->beforeUpdate($this->resetPassword);
    }

    public function testUpdateWithCorrectResetKey() {
        $user = new User();
        $user->passwordResetKeyHash = 'resetKey';

        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->with(self::EMAIL)
            ->willReturn($user)
        ;
        $this->pwHasher->expects(self::once())
            ->method('verify')
            ->willReturn(true)
        ;
        $this->pwHasher->expects(self::once())
            ->method('hash')
            ->willReturnCallback(fn ($raw) => md5($raw))
        ;

        $this->resetPassword->emailBase64 = self::EMAILBASE64;
        $this->resetPassword->resetKey = 'myKey';
        $this->resetPassword->password = 'newPassword';

        $this->dataPersister->beforeUpdate($this->resetPassword);

        self::assertThat($user->password, self::equalTo(md5('newPassword')));
        self::assertThat($user->passwordResetKeyHash, self::isEmpty());
    }
}
