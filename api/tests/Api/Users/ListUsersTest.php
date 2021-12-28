<?php

namespace App\Tests\Api\Users;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListUsersTest extends ECampApiTestCase {
    public function testListUsersIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/users');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListUsersIsDeniedForLoggedInUser() {
        static::createClientWithCredentials()->request('GET', '/users');
        $this->assertResponseStatusCodeSame(403);
    }
}
