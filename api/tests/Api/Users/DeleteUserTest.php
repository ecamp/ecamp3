<?php

namespace App\Tests\Api\Users;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteUserTest extends ECampApiTestCase {
    public function testDeleteUserIsDeniedForAnonymousUser() {
        $user = static::$fixtures['user1manager'];
        static::createBasicClient()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteUserIsDeniedForRelatedUser() {
        $user2 = static::$fixtures['user2member'];
        static::createClientWithCredentials()->request('DELETE', '/users/'.$user2->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteUserIsDeniedForDifferentUser() {
        $user2 = static::$fixtures['user4unrelated'];
        static::createClientWithCredentials()->request('DELETE', '/users/'.$user2->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteUserIsDeniedForSelf() {
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
