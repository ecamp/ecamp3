<?php

namespace App\Tests\Api\Users;

use App\Repository\UserRepository;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteUserTest extends ECampApiTestCase {
    public function testDeleteSingleUserIsDeniedToAnonymousUser() {
        $user = static::$fixtures['user_1'];
        static::createClient()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteSingleUserIsDeniedForDifferentUser() {
        $user2 = static::$fixtures['user_2'];
        static::createClientWithCredentials()->request('DELETE', '/users/'.$user2->getId());
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleUserIsDeniedForSelf() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testGetSingleUserIsAllowedForAdmin() {
        $user = static::$fixtures['user_1'];
        $this->assertNotNull(static::$container->get(UserRepository::class)->findOneBy(['username' => $user->getUsername()]));

        static::createClientWithAdminCredentials()->request('DELETE', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(static::$container->get(UserRepository::class)->findOneBy(['username' => $user->getUsername()]));
    }
}
