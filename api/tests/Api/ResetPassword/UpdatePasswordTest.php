<?php

namespace App\Tests\Api\ResetPassword;

use App\Entity\User;
use App\Security\ReCaptcha\ReCaptchaWrapper;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use ReCaptcha\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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

    #[\Override]
    public function setUp(): void {
        parent::setUp();
        $this->client = static::createBasicClient();
        $this->client->disableReboot();

        $this->user = static::getFixture('user1manager');
        $this->passwordResetKey = 'dGVzdEBleGFtcGxlLmNvbSM3OWIwZGVkNmEzNGRmNzJkMTU4MzEzNzFlNGVjZWM1ZGYwMWU0ZTc5YzM3ODg4N2IzYjAzOTQzNWNmMmM0MWFj';
        $this->getEntityManager()->createQueryBuilder()->update(User::class, 'u')
            ->set('u.passwordResetKeyHash', ':hash')
            ->where('u.id = :id')
            ->setParameter('hash', '$2y$13$QjJWNEV/CM1Urnx2kCdJF.Fxj6dFmELvVSxcXkjuzpKYYZYFRmS9q')
            ->setParameter('id', $this->user->getId())
            ->getQuery()->execute()
        ;
    }

    #[AllowMockObjectsWithoutExpectations]
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

    #[AllowMockObjectsWithoutExpectations]
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

    #[AllowMockObjectsWithoutExpectations]
    public function testPatchResetPasswordAllowsLongPassword() {
        $this->mockRecaptcha();
        $this->client->request('PATCH', '/auth/reset_password/'.$this->passwordResetKey, ['json' => [
            'password' => str_repeat('a', 128),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testPatchResetPasswordRejectsPasswordFromLocalCompromisedPasswordList() {
        $this->mockRecaptcha();
        $password = file(__DIR__.'/../../../src/Validator/PwnedPasswords/password-list.txt', FILE_IGNORE_NEW_LINES)[0];
        $this->client->request('PATCH', '/auth/reset_password/'.$this->passwordResetKey, ['json' => [
            'password' => $password,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This password has appeared in a data breach and cannot be used. Please choose a different password.',
                ],
            ],
        ]);
    }

    #[AllowMockObjectsWithoutExpectations]
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

    /**
     * @throws NonUniqueResultException
     * @throws TransportExceptionInterface
     * @throws NoResultException
     */
    #[AllowMockObjectsWithoutExpectations]
    public function testPatchResetPasswordActivatesUser() {
        $this->mockRecaptcha();
        $this->getEntityManager()->createQueryBuilder()
            ->update(User::class, 'u')
            ->set('u.state', ':state')
            ->where('u.id = :id')
            ->setParameter('state', User::STATE_REGISTERED)
            ->setParameter('id', $this->user->getId())
            ->getQuery()
            ->execute()
        ;

        $this->client->request(
            'POST',
            '/authentication_token',
            [
                'json' => [
                    'identifier' => $this->user->getEmail(),
                    'password' => 'test',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(401);

        $newPassword = 'definitely-not-in-password-list-2026';
        $this->client->request(
            'PATCH',
            '/auth/reset_password/'.$this->passwordResetKey,
            [
                'json' => [
                    'password' => $newPassword,
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(200);

        $this->client->request(
            'POST',
            '/authentication_token',
            [
                'json' => [
                    'identifier' => $this->user->getEmail(),
                    'password' => $newPassword,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(204);
    }

    protected function mockRecaptcha($shouldReturnSuccess = true) {
        $container = static::getContainer();
        $recaptcha = $this->createStub(ReCaptchaWrapper::class);
        $response = $this->createStub(Response::class);
        $recaptcha->method('verify')->willReturn($response);
        $response->method('isSuccess')->willReturn($shouldReturnSuccess);
        $container->set(ReCaptchaWrapper::class, $recaptcha);
    }
}
