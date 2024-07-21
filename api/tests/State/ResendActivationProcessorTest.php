<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use App\DTO\UserActivation;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ReCaptcha\ReCaptchaWrapper;
use App\Service\MailService;
use App\State\ResendActivationProcessor;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReCaptcha\Response;

use function PHPUnit\Framework\isNull;
use function PHPUnit\Framework\logicalNot;

/**
 * @internal
 */
class ResendActivationProcessorTest extends TestCase {
    public const EMAIL = 'a@b.com';

    private UserActivation $userActivation;

    private MockObject|Response $recaptchaResponse;
    private MockObject|UserRepository $userRepository;
    private MailService|MockObject $mailService;

    /**
     * @throws Exception
     */
    protected function setUp(): void {
        $this->userActivation = new UserActivation();

        $this->recaptchaResponse = $this->createMock(Response::class);
        $recaptcha = $this->createMock(ReCaptchaWrapper::class);
        $entityManager = $this->createMock(EntityManager::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->mailService = $this->createMock(MailService::class);

        $recaptcha->expects(self::any())
            ->method('verify')
            ->willReturn($this->recaptchaResponse)
        ;

        $this->processor = new ResendActivationProcessor(
            $recaptcha,
            $entityManager,
            $this->userRepository,
            $this->mailService
        );
    }

    public function testCreateRequiresReCaptcha() {
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(false)
        ;
        $this->userActivation->recaptchaToken = 'token';

        $this->expectException(\Exception::class);
        $this->processor->process($this->userActivation, new Post());
    }

    public function testCreateWithUnknownEmailDoesNothing() {
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

        $this->userActivation->recaptchaToken = 'token';
        $this->userActivation->email = self::EMAIL;

        $data = $this->processor->process($this->userActivation, new Post());

        self::assertThat($data, self::isNull());
    }

    #[TestWith([User::STATE_REGISTERED])]
    public function testCreateWithInactiveUserResendsActivationeMail(string $state) {
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true)
        ;
        $user = new User();
        $user->state = $state;
        $user->activationKey = 'activationKey';
        $user->activationKeyHash = 'activationKey';

        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->with(self::EMAIL)
            ->willReturn($user)
        ;

        $this->mailService->expects(self::once())
            ->method('sendUserActivationMail')
            ->with($user, logicalNot(isNull()))
        ;

        $this->userActivation->recaptchaToken = 'token';
        $this->userActivation->email = self::EMAIL;
        $data = $this->processor->process($this->userActivation, new Post());

        self::assertThat($data, self::isNull());
    }

    #[TestWith([User::STATE_NONREGISTERED])]
    #[TestWith([User::STATE_ACTIVATED])]
    #[TestWith([User::STATE_DELETED])]
    public function testCreateWithActiveOrDeletedUserDoesNothing(string $state) {
        $this->recaptchaResponse->expects(self::once())
            ->method('isSuccess')
            ->willReturn(true)
        ;
        $user = new User();
        $user->state = $state;
        $user->activationKey = 'activationKey';
        $user->activationKeyHash = 'activationKey';

        $this->userRepository->expects(self::once())
            ->method('loadUserByIdentifier')
            ->with(self::EMAIL)
            ->willReturn($user)
        ;

        $this->mailService->expects(self::never())
            ->method('sendUserActivationMail')
        ;

        $this->userActivation->recaptchaToken = 'token';
        $this->userActivation->email = self::EMAIL;
        $data = $this->processor->process($this->userActivation, new Post());

        self::assertThat($data, self::isNull());
    }
}
