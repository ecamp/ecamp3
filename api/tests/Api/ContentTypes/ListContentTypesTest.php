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
            'totalItems' => 3,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('contentType1')],
            ['href' => $this->getIriFor('contentType2')],
            ['href' => $this->getIriFor('contentTypeColumnLayout')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentTypesIsAllowedForLoggedInUser() {
        $response = static::createClientWithCredentials()->request('GET', '/content_types');
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
            ['href' => $this->getIriFor('contentType1')],
            ['href' => $this->getIriFor('contentType2')],
            ['href' => $this->getIriFor('contentTypeColumnLayout')],
        ], $response->toArray()['_links']['items']);
    }
}
