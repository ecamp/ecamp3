<?php

namespace App\Tests\Api\Camps;

use App\Repository\CampRepository;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListCampsTest extends ECampApiTestCase {
    public function testListCampsIsDeniedToAnonymousUser() {
        static::createClient()->request('GET', '/camps');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListCampsIsAllowedForLoggedInUserButFiltered() {
        $response = static::createClientWithCredentials()->request('GET', '/camps');
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
            ['href' => $this->getIriFor('camp1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampsListsAllCampsForAdmin() {
        $client = static::createClientWithAdminCredentials();
        $response = $client->request('GET', '/camps');
        $totalCamps = count(static::getContainer()->get(CampRepository::class)->findAll());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => $totalCamps,
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEquals($totalCamps, count($response->toArray(true)['_embedded']['items']));
    }
}
