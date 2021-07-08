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
        static::createClientWithCredentials()->request('GET', '/users');
        $this->assertResponseStatusCodeSame(200);
        $userIri = $this->getIriConverter()->getIriFromItem(static::$fixtures['user1']);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_embedded' => [
                'items' => [
                    [
                        '_links' => [
                            'self' => [
                                'href' => $userIri,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
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
