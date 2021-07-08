<?php

namespace App\Tests\Api\Users;

use App\Repository\UserRepository;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteUserTest extends ECampApiTestCase {
    public function testDeleteUserIsDeniedToAnonymousUser() {
        $user = static::$fixtures['user1'];
        static::createClient()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteUserIsDeniedForDifferentUser() {
        $user2 = static::$fixtures['user2'];
        static::createClientWithCredentials()->request('DELETE', '/users/'.$user2->getId());
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteUserIsDeniedForSelf() {
        $user = static::$fixtures['user1'];
        static::createClientWithCredentials()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteUserIsAllowedForAdmin() {
        $user = static::$fixtures['user3'];
        $this->assertNotNull(static::getContainer()->get(UserRepository::class)->findOneBy(['username' => $user->getUsername()]));

        static::createClientWithAdminCredentials()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(static::getContainer()->get(UserRepository::class)->findOneBy(['username' => $user->getUsername()]));
    }

    public function testDeleteUserIsNotAllowedForAdminIfUserStillOwnsCamps() {
        $user = static::$fixtures['user1'];
        $this->assertNotNull(static::getContainer()->get(UserRepository::class)->findOneBy(['username' => $user->getUsername()]));
        static::createClientWithAdminCredentials()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
