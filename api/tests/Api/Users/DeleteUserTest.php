<?php

namespace App\Tests\Api\Users;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteUserTest extends ECampApiTestCase {
    public function testDeleteUserIsDeniedToAnonymousUser() {
        $user = static::$fixtures['user1manager'];
        static::createClient()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteUserIsDeniedForDifferentUser() {
        $user2 = static::$fixtures['user2member'];
        static::createClientWithCredentials()->request('DELETE', '/users/'.$user2->getId());
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
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
