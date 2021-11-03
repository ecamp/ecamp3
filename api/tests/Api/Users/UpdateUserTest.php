<?php

namespace App\Tests\Api\Users;

use App\Entity\User;
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
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchUserIsAllowedForSelf() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Linux',
        ]);
    }

    public function testPatchUserTrimsEmail() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => ' trimmed@example.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'email' => 'trimmed@example.com',
        ]);
    }

    public function testPatchUserValidatesBlankEmail() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchUserValidatesInvalidEmail() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => 'hello@sunrise',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.',
                ],
            ],
        ]);
    }

    public function testPatchUserValidatesLongEmail() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => 'test-with-a-very-long-email-address-which-is-not-really-realistic@example.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is too long. It should have 64 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchUserValidatesDuplicateEmail() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => static::$fixtures['user2member']->email,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function testPatchUserTrimsFirstThenValidatesDuplicateEmail() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => ' '.static::$fixtures['user2member']->email,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function testPatchUserDisallowsChangingUsername() {
        /** @var User $user */
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'username' => 'bi-pi',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("username" is unknown).',
        ]);
    }

    public function testPatchUserTrimsFirstname() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'firstname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'firstname' => 'Hello',
        ]);
    }

    public function testPatchUserCleansHTMLFromFirstname() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'firstname' => '<script>alert(1)</script>Hello',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'firstname' => 'Hello',
        ]);
    }

    public function testPatchUserTrimsSurname() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'surname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'surname' => 'Hello',
        ]);
    }

    public function testPatchUserCleansHTMLFromSurname() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'surname' => '<script>alert(1)</script>Hello',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'surname' => 'Hello',
        ]);
    }

    public function testPatchUserTrimsNickname() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'nickname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Hello',
        ]);
    }

    public function testPatchUserCleansHTMLFromNickname() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'nickname' => '<script>alert(1)</script>Hello',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Hello',
        ]);
    }

    public function testPatchUserTrimsLanguage() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'language' => "\tde ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'language' => 'de',
        ]);
    }

    public function testPatchUserValidatesInvalidLanguage() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'language' => 'französisch',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'language',
                    'message' => 'The value you selected is not a valid choice.',
                ],
            ],
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
                    'message' => 'This value is too short. It should have 8 characters or more.',
                ],
            ],
        ]);
    }
}
