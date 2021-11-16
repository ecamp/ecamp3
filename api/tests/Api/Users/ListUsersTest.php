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

    public function testListUsersIsAllowedForLoggedInUserButFiltered() {
        $response = static::createClientWithCredentials()->request('GET', '/users');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 8,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('user1manager')],
            ['href' => $this->getIriFor('user2member')],
            ['href' => $this->getIriFor('user3guest')],
            ['href' => $this->getIriFor('user4unrelated')],
            ['href' => $this->getIriFor('user5inactive')],
            ['href' => $this->getIriFor('user6invited')],
            ['href' => $this->getIriFor('admin')],
            ['href' => $this->getIriFor('userWithoutCampCollaborations')],
        ], $response->toArray()['_links']['items']);
    }
}
