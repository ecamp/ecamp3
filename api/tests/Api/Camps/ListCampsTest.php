<?php

namespace App\Tests\Api\Camps;

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
            'totalItems' => 3,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('camp1')],
            ['href' => $this->getIriFor('camp2')],
            ['href' => $this->getIriFor('campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
