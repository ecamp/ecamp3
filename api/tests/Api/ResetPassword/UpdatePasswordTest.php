<?php

namespace App\Tests\Api\ResetPassword;

use App\Entity\User;
use App\Security\ReCaptcha\ReCaptcha;
use App\Tests\Api\ECampApiTestCase;
use ReCaptcha\Response;

/**
 * @internal
 */
class UpdatePasswordTest extends ECampApiTestCase {
    // TODO tests for normal operation of password update
    // TODO extensive tests for authentication checks using the reset password token
    // TODO tests for recaptcha check
    // TODO tests for requesting a password reset and retrieving the password reset data, in separate test files

    private ?User $user;
    private ?string $passwordResetKey;
    private $client;

    public function setUp(): void {
        parent::setUp();
        $this->client = static::createBasicClient();
        $this->client->disableReboot();

        $this->user = static::$fixtures['user1manager'];
        $this->passwordResetKey = 'dGVzdEBleGFtcGxlLmNvbSM3OWIwZGVkNmEzNGRmNzJkMTU4MzEzNzFlNGVjZWM1ZGYwMWU0ZTc5YzM3ODg4N2IzYjAzOTQzNWNmMmM0MWFj';
        $this->getEntityManager()->createQueryBuilder()->update(User::class, 'u')
            ->set('u.passwordResetKeyHash', ':hash')
            ->where('u.id = :id')
            ->setParameter('hash', '$2y$13$QjJWNEV/CM1Urnx2kCdJF.Fxj6dFmELvVSxcXkjuzpKYYZYFRmS9q')
            ->setParameter('id', $this->user->getId())
            ->getQuery()->execute();
    }

    public function testPatchResetPasswordValidatesBlankPassword() {
        $this->mockRecaptcha();
        $this->client->request('PATCH', '/auth/reset_password/'.$this->passwordResetKey, ['json' => [
            'password' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value is too short. It should have 12 characters or more.',
                ],
            ],
        ]);
    }

    public function testPatchResetPasswordValidatesShortPassword() {
        $this->mockRecaptcha();
        $this->client->request('PATCH', '/auth/reset_password/'.$this->passwordResetKey, ['json' => [
            'password' => 'only11chars',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value is too short. It should have 12 characters or more.',
                ],
            ],
        ]);
    }

    public function testPatchResetPasswordAllowsLongPassword() {
        $this->mockRecaptcha();
        $this->client->request('PATCH', '/auth/reset_password/'.$this->passwordResetKey, ['json' => [
            'password' => str_repeat('a', 128),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testPatchResetPasswordValidatesUnreasonablyLongPassword() {
        $this->mockRecaptcha();
        $this->client->request('PATCH', '/auth/reset_password/'.$this->passwordResetKey, ['json' => [
            'password' => str_repeat('a', 129),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    protected function mockRecaptcha($shouldReturnSuccess = true) {
        $container = static::getContainer();
        $recaptcha = $this->createMock(ReCaptcha::class);
        $response = $this->createMock(Response::class);
        $recaptcha->expects(self::any())
            ->method('verify')
            ->willReturn($response)
        ;
        $response->expects(self::any())
            ->method('isSuccess')
            ->willReturn($shouldReturnSuccess)
        ;
        $container->set(ReCaptcha::class, $recaptcha);
    }
}
