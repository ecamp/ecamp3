<?php

namespace App\Tests\DataPersister;

use App\DataPersister\UserDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\User;
use App\Service\MailService;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 */
class UserDataPersisterTest extends TestCase {
    private UserDataPersister $dataPersister;
    private MockObject|UserPasswordHasherInterface $userPasswordHasher;
    private MockObject|MailService $mailService;
    private User $user;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->user = new User();

        $this->userPasswordHasher = $this->createMock(UserPasswordHasher::class);
        $this->mailService = $this->createMock(MailService::class);
        $dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->dataPersister = new UserDataPersister(
            $dataPersisterObservable,
            $this->userPasswordHasher,
            $this->mailService
        );
    }

    public function testDoesNotHashWhenNoPasswordIsSet() {
        // given
        $this->userPasswordHasher->expects($this->never())->method('hashPassword');

        // when
        /** @var User $result */
        $data = $this->dataPersister->beforeCreate($this->user);

        // then
        $this->assertNull($data->password);
        $this->assertNull($data->plainPassword);
    }

    public function testHashesPasswordWhenPlainPasswordIsSet() {
        // given
        $this->user->plainPassword = 'test plain password';
        $this->userPasswordHasher->expects($this->once())->method('hashPassword')->willReturn('test hash');

        // when
        /** @var User $result */
        $data = $this->dataPersister->beforeCreate($this->user);

        // then
        $this->assertEquals('test hash', $data->password);
        $this->assertNull($data->plainPassword);
    }

    public function testCreateAndSendActivationKey() {
        // given
        $this->mailService->expects($this->once())->method('sendUserActivationMail');

        // when
        /** @var User $data */
        $data = $this->dataPersister->beforeCreate($this->user);
        $this->dataPersister->afterCreate($this->user);

        // then
        $this->assertNotNull($data->activationKeyHash);
    }

    public function testSetsStateToRegisteredBeforeCreate() {
        // when
        /** @var User $data */
        $data = $this->dataPersister->beforeCreate($this->user);

        // then
        self::assertThat($data->state, self::equalTo(User::STATE_REGISTERED));
    }

    public function testHashesPasswordBeforeUpdate() {
        // given
        $this->user->plainPassword = 'test plain password';
        $this->userPasswordHasher->expects($this->once())->method('hashPassword')->willReturn('test hash');

        // when
        /** @var User $result */
        $data = $this->dataPersister->beforeUpdate($this->user);

        // then
        $this->assertEquals('test hash', $data->password);
        $this->assertNull($data->plainPassword);
    }

    public function testThrowsIfActivationKeyIsWrongForOnActivate() {
        $this->user->activationKey = 'activation key';
        $this->user->activationKeyHash = 'wrong hash';

        $this->expectException(Exception::class);
        $this->dataPersister->onActivate($this->user);
    }

    /**
     * @throws Exception
     */
    public function testActivatesUserIfActivationKeyIsCorrect() {
        $this->user->activationKey = 'activation key';
        $this->user->activationKeyHash = md5($this->user->activationKey);

        /** @var User $activatedUser */
        $activatedUser = $this->dataPersister->onActivate($this->user);
        self::assertThat($activatedUser->state, self::equalTo(User::STATE_ACTIVATED));
        self::assertThat($activatedUser->activationKeyHash, self::isNull());
        self::assertThat($activatedUser->activationKey, self::isNull());
    }
}
