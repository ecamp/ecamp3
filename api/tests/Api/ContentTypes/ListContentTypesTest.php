<?php

namespace App\Tests\Api\ContentTypes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListContentTypesTest extends ECampApiTestCase {
    public function testListContentTypesIsAllowedForAnonymousUser() {
        $response = static::createBasicClient()->request('GET', '/content_types');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 11,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);

        $this->assertCount(11, $response->toArray()['_links']['items']);
    }

    public function testListContentTypesIsAllowedForLoggedInUser() {
        $response = static::createClientWithCredentials()->request('GET', '/content_types');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 11,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertCount(11, $response->toArray()['_links']['items']);
    }
}
