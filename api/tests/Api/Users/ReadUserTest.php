<?php

namespace App\Tests\Api\Users;

use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadUserTest extends ECampApiTestCase {
    public function testGetSingleUserIsDeniedToAnonymousUser() {
        $user = static::$fixtures['user1manager'];
        static::createClient()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleUserIsDeniedForDifferentUser() {
        $user2 = static::$fixtures['user2member'];
        static::createClientWithCredentials()->request('GET', '/users/'.$user2->getId());
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleUserIsAllowedForSelf() {
        /** @var User $user */
        $user = static::$fixtures['user1manager'];
        static::createClientWithCredentials()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $user->getId(),
            'email' => $user->email,
            'username' => $user->username,
            'firstname' => $user->firstname,
            'surname' => $user->surname,
            'nickname' => $user->nickname,
            'language' => $user->language,
            'displayName' => 'Bi-Pi',
        ]);
    }
}
