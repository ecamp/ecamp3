<?php

namespace App\Tests\Api\Users;

use App\Tests\Api\ECampApiTestCase;

class UpdateUserTest extends ECampApiTestCase {

    public function testPatchUserIsDeniedToAnonymousUser() {
        $user = static::$fixtures['user_1'];
        static::createClient()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'nickname' => 'Linux'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found'
        ]);
    }

    public function testPatchUserIsDeniedForDifferentUser() {
        $user2 = static::$fixtures['user_2'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user2->getId(), ['json' => [
            'nickname' => 'Linux'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found'
        ]);
    }

    public function testPatchUserIsAllowedForSelf() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'nickname' => 'Linux'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Linux',
        ]);
    }

    public function testPatchUserIsAllowedForAdmin() {
        $user = static::$fixtures['user_1'];
        static::createClientWithAdminCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'nickname' => 'Linux'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Linux',
        ]);
    }

    public function testPatchUserValidatesBlankEmail() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => ''
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.'
                ]
            ]
        ]);
    }

    public function testPatchUserValidatesInvalidEmail() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => 'hello@sunrise'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.'
                ]
            ]
        ]);
    }

    public function testPatchUserValidatesLongEmail() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'email' => 'test-with-a-very-long-email-address-which-is-not-really-realistic@example.com'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is too long. It should have 64 characters or less.'
                ]
            ]
        ]);
    }

    public function testPatchUserValidatesBlankUsername() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'username' => ''
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value should not be blank.'
                ]
            ]
        ]);
    }

    public function testPatchUserValidatesInvalidUsername() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'username' => 'a*b'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is not valid.'
                ]
            ]
        ]);
    }

    public function testPatchUserValidatesLongUsername() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'username' => 'abcdefghijklmnopqrstuvwxyz-the-alphabet'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is too long. It should have 32 characters or less.'
                ]
            ]
        ]);
    }

    public function testPatchUserValidatesLongLanguage() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'language' => 'fr_CH.some-ridiculously-long-extension-which-is-technically-a-valid-ICU-locale'
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'language',
                    'message' => 'This value is too long. It should have 20 characters or less.'
                ]
            ]
        ]);
    }

    public function testPatchUserValidatesBlankPassword() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('PATCH', '/users/'.$user->getId(), ['json' => [
            'password' => ''
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value is too short. It should have 8 characters or more.'
                ]
            ]
        ]);
    }
}
