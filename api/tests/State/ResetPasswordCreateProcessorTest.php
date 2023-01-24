<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use App\DTO\ResetPassword;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ReCaptcha\ReCaptcha;
use App\Service\MailService;
use App\State\ResetPasswordCreateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReCaptcha\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @internal
 */
class ResetPasswordCreateProcessorTest extends TestCase {
    public const EMAIL = 'a@b.com';
    public const EMAILBASE64 = 'YUBiLmNvbQ==';

    private ResetPassword $resetPassword;

    private MockObject|ReCaptcha $recaptcha;
    private MockObject|Response $recaptchaResponse;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|UserRepository $userRepository;
    private MockObject|PasswordHasherFactoryInterface $pwHasherFactory;
    private MockObject|PasswordHasherInterface $pwHasher;
    private MockObject|MailService $mailService;
    private ResetPasswordCreateProcessor $processor;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->resetPassword = new ResetPassword();

        $this->recaptchaResponse = $this->createMock(Response::class);
        $this->recaptcha = $this->createMock(ReCaptcha::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->pwHasherFactory = $this->createMock(PasswordHasherFactory::class);
        $this->pwHasher = $this->createMock(PasswordHasherInterface::class);
        $this->mailService = $this->createMock(MailService::class);

        $this->recaptcha->expects(self::any())
            ->method('verify')
            ->willReturn($this->recaptchaResponse)
        ;

        $this->pwHasherFactory->expects(self::any())
            ->method('getPasswordHasher')
            ->willReturn($this->pwHasher)
        ;

        $this->processor = new ResetPasswordCreateProcessor(
            $this->recaptcha,
            $this->entityManager,
            $this->userRepository,
            $this->pwHasherFactory,
            $this->mailService
        );
    }

    public function testCreateRequiresReCaptcha() {
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(false)
        ;
        $this->resetPassword->recaptchaToken = 'token';

        $this->expectException(\Exception::class);
        $this->processor->process($this->resetPassword, new Post());
    }

    public function testCreateWithUnknownEmailDoesNotCreateResetKey() {
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true)
        ;
        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->willReturn(null)
        ;
        $this->mailService->expects(self::never())
            ->method('sendPasswordResetLink')
        ;

        $this->resetPassword->recaptchaToken = 'token';
        $this->resetPassword->email = self::EMAIL;

        $data = $this->processor->process($this->resetPassword, new Post());
    }

    public function testCreateWithKnowneMailCreatesResetKey() {
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true)
        ;
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

        $this->resetPassword->recaptchaToken = 'token';
        $this->resetPassword->email = self::EMAIL;
        $data = $this->processor->process($this->resetPassword, new Post());

        self::assertThat($data->id, self::logicalNot(self::isNull()));
    }
}
