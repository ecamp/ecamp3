<?php

namespace App\Tests\Api\Users;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateUserTest extends ECampApiTestCase {
    public function testPatchUserIsDeniedForAnonymousUser() {
        $user = static::$fixtures['user1manager'];
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
        $user2 = static::$fixtures['user2member'];
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
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'password' => 'passwordpassword',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'displayName' => 'Bi-Pi',
        ]);
    }

    public function testPatchUserValidatesBlankPassword() {
        $user = static::$fixtures['user1manager'];
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
        $user = static::$fixtures['user1manager'];
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

    /**
     * @dataProvider notWriteableUserProperties
     */
    public function testNotWriteableProperties(string $property) {
        $user = static::$fixtures['user1manager'];
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

    public static function notWriteableUserProperties(): array {
        return [
            'activationKeyHash' => ['activationKeyHash'],
            'passwordResetKeyHash' => ['passwordResetKeyHash'],
        ];
    }
}
