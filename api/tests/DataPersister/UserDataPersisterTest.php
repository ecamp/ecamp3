<?php

namespace App\Tests\DataPersister;

use App\DataPersister\UserDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\User;
use App\Security\ReCaptcha\ReCaptcha;
use App\Service\MailService;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReCaptcha\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 */
class UserDataPersisterTest extends TestCase {
    private UserDataPersister $dataPersister;
    private MockObject|ReCaptcha $recaptcha;
    private MockObject|Response $recaptchaResponse;
    private MockObject|UserPasswordHasherInterface $userPasswordHasher;
    private MockObject|MailService $mailService;
    private User $user;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->user = new User();

        $this->recaptchaResponse = $this->createMock(Response::class);
        $this->recaptcha = $this->createMock(ReCaptcha::class);
        $this->recaptcha->expects(self::any())
            ->method('verify')
            ->willReturn($this->recaptchaResponse)
        ;

        $this->userPasswordHasher = $this->createMock(UserPasswordHasher::class);
        $this->mailService = $this->createMock(MailService::class);
        $dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->dataPersister = new UserDataPersister(
            $dataPersisterObservable,
            $this->recaptcha,
            $this->userPasswordHasher,
            $this->mailService
        );
    }

    public function testCreateRequiresReCaptcha() {
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(false)
        ;

        $this->expectException(Exception::class);
        $this->dataPersister->beforeCreate($this->user);
    }

    public function testDoesNotHashWhenNoPasswordIsSet() {
        // given
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true)
        ;
        $this->userPasswordHasher->expects($this->never())->method('hashPassword');

        // when
        /** @var User $data */
        $data = $this->dataPersister->beforeCreate($this->user);

        // then
        $this->assertNull($data->password);
        $this->assertNull($data->plainPassword);
    }

    public function testHashesPasswordWhenPlainPasswordIsSet() {
        // given
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true)
        ;
        $this->user->plainPassword = 'test plain password';
        $this->userPasswordHasher->expects($this->once())->method('hashPassword')->willReturn('test hash');

        // when
        /** @var User $data */
        $data = $this->dataPersister->beforeCreate($this->user);

        // then
        $this->assertEquals('test hash', $data->password);
        $this->assertNull($data->plainPassword);
    }

    public function testCreateAndSendActivationKey() {
        // given
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true)
        ;
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
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true)
        ;

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
        /** @var User $data */
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
