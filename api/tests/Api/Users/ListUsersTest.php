<?php

namespace App\Tests\Api\Users;

use App\Repository\UserRepository;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListUsersTest extends ECampApiTestCase {
    public function testListUsersIsDeniedToAnonymousUser() {
        static::createClient()->request('GET', '/users');
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
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('user1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListUsersListsAllUsersForAdmin() {
        $client = static::createClientWithAdminCredentials();
        $response = $client->request('GET', '/users');
        $totalUsers = count(static::getContainer()->get(UserRepository::class)->findAll());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => $totalUsers,
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEquals($totalUsers, count($response->toArray(true)['_embedded']['items']));
    }
}
