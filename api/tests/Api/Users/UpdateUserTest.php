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
}
