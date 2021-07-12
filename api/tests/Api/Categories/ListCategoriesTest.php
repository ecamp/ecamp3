<?php

namespace App\Tests\Api\Categories;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListCategoriesTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListCategoriesIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/categories');
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
            ['href' => $this->getIriFor('category1')],
            ['href' => $this->getIriFor('category2')],
            ['href' => $this->getIriFor('category1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCategoriesFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/categories?camp=/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 2,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('category1')],
            ['href' => $this->getIriFor('category2')],
        ], $response->toArray()['_links']['items']);
    }
}
