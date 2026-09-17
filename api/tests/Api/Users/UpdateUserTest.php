<?php

namespace App\Tests\Api\Users;

use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class UpdateUserTest extends ECampApiTestCase {
    public function testPatchUserIsDeniedForAnonymousUser() {
        $user = static::getFixture('user1manager');
        static::createBasicClient()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchUserIsDeniedForDifferentUser() {
        $user2 = static::getFixture('user2member');
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user2->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchUserIsAllowedForSelf() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'password' => 'passwordpassword',
            'currentPassword' => 'test',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'displayName' => 'Bi-Pi',
        ]);
    }

    public function testPatchUserValidatesBlankPassword() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
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

    public function testPatchUserDoesNotAllowPatchingProfile() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/users/'.$user->getId(),
            [
                'json' => [
                    'profile' => [
                        'nickname' => 'blabla',
                    ],
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Extra attributes are not allowed ("profile" is unknown).',
        ]);
    }

    #[DataProvider('notWriteableUserProperties')]
    public function testNotWriteableProperties(string $property) {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/users/'.$user->getId(),
            [
                'json' => [
                    $property => 'something',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => "Extra attributes are not allowed (\"{$property}\" is unknown).",
        ]);
    }

    public static function notWriteableUserProperties(): \Iterator {
        yield 'activationKeyHash' => ['activationKeyHash'];

        yield 'passwordResetKeyHash' => ['passwordResetKeyHash'];
    }

    public function testPatchUserChangingPasswordRequiresCurrentPassword() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'password' => 'passwordpassword',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'currentPassword',
                    'message' => 'The current password you entered is incorrect.',
                ],
            ],
        ]);
    }

    public function testPatchUserChangingPasswordRejectsBlankCurrentPassword() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'password' => 'passwordpassword',
            'currentPassword' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'currentPassword',
                    'message' => 'The current password you entered is incorrect.',
                ],
            ],
        ]);
    }

    public function testPatchUserChangingPasswordRejectsWrongCurrentPassword() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'password' => 'passwordpassword',
            'currentPassword' => 'wrong-current-password',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'currentPassword',
                    'message' => 'The current password you entered is incorrect.',
                ],
            ],
        ]);
    }

    public function testPatchUserChangingPasswordAcceptsCorrectCurrentPassword() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'password' => 'passwordpassword',
            'currentPassword' => 'test',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'displayName' => 'Bi-Pi',
        ]);
    }

    public function testPatchUserCanLoginWithNewPasswordAfterChangingPassword() {
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'password' => 'passwordpassword',
            'currentPassword' => 'test',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);

        // the new password works
        static::createBasicClient()->request('POST', '/authentication_token', ['json' => [
            'identifier' => $user->getEmail(),
            'password' => 'passwordpassword',
        ]]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertResponseHasHeader('Set-Cookie');

        // the old password no longer works
        static::createBasicClient()->request('POST', '/authentication_token', ['json' => [
            'identifier' => $user->getEmail(),
            'password' => 'test',
        ]]);
        $this->assertResponseStatusCodeSame(401);
    }
}
