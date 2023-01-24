<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Profile;
use App\Entity\User;
use App\Service\MailService;
use App\State\ProfileUpdateProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @internal
 */
class ProfileUpdateProcessorTest extends TestCase {
    private ProfileUpdateProcessor $processor;
    private MockObject|PasswordHasherFactoryInterface $pwHasherFactory;
    private MockObject|PasswordHasherInterface $pwHasher;
    private MockObject|MailService $mailService;
    private Profile $profile;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->profile = new Profile();
        $this->profile->user = new User();

        $this->pwHasherFactory = $this->createMock(PasswordHasherFactoryInterface::class);
        $this->pwHasher = $this->createMock(PasswordHasherInterface::class);
        $this->mailService = $this->createMock(MailService::class);
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);

        $this->pwHasherFactory->expects(self::any())
            ->method('getPasswordHasher')
            ->willReturn($this->pwHasher)
        ;

        $this->processor = new ProfileUpdateProcessor(
            $decoratedProcessor,
            $this->pwHasherFactory,
            $this->mailService
        );
    }

    public function testSetNewEmail() {
        // given
        $this->pwHasher->expects(self::once())
            ->method('hash')
            ->willReturnCallback(fn ($raw) => md5($raw))
        ;
        $this->profile->newEmail = 'new@mail.com';

        // when
        $this->processor->onBefore($this->profile, new Patch());

        // then
        $this->assertEquals('new@mail.com', $this->profile->untrustedEmail);
        $this->assertNotNull($this->profile->untrustedEmailKey);
        $this->assertNotNull($this->profile->untrustedEmailKeyHash);
    }

    public function testSendVerificationMail() {
        // given
        $this->profile->untrustedEmailKey = 'abc';
        $this->mailService->expects($this->once())->method('sendEmailVerificationMail');

        // when
        $this->processor->onAfter($this->profile, new Patch());

        // then
        $this->assertNull($this->profile->untrustedEmailKey);
    }

    public function testChangeEmail() {
        // given
        $this->pwHasher->expects(self::once())
            ->method('verify')
            ->willReturn(true)
        ;
        $this->profile->email = 'old@mail.com';
        $this->profile->untrustedEmail = 'new@mail.com';
        $this->profile->untrustedEmailKey = 'abc';
        $this->profile->untrustedEmailKeyHash = 'abc';

        // when
        $this->processor->onBefore($this->profile, new Patch());

        // then
        $this->assertEquals('new@mail.com', $this->profile->email);
        $this->assertNull($this->profile->untrustedEmail);
        $this->assertNull($this->profile->untrustedEmailKey);
        $this->assertNull($this->profile->untrustedEmailKeyHash);
    }
}
