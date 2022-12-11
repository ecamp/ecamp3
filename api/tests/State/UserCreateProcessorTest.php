<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Security\ReCaptcha\ReCaptcha;
use App\Service\MailService;
use App\State\UserCreateProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReCaptcha\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 */
class UserCreateProcessorTest extends TestCase {
    private UserCreateProcessor $processor;
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
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->processor = new UserCreateProcessor(
            $decoratedProcessor,
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

        $this->expectException(\Exception::class);
        $this->processor->onBefore($this->user, new Post());
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
        $data = $this->processor->onBefore($this->user, new Post());

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
        $data = $this->processor->onBefore($this->user, new Post());

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
        $data = $this->processor->onBefore($this->user, new Post());
        $this->processor->onAfter($this->user, new Post());

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
        $data = $this->processor->onBefore($this->user, new Post());

        // then
        self::assertThat($data->state, self::equalTo(User::STATE_REGISTERED));
    }
}
