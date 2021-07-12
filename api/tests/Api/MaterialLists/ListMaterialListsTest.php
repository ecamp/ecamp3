<?php

namespace App\Tests\Api\MaterialLists;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListMaterialListsTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListMaterialListsIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/material_lists');
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
            ['href' => $this->getIriFor('materialList1')],
            ['href' => $this->getIriFor('materialList2')],
            ['href' => $this->getIriFor('materialList1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListMaterialListsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/material_lists?camp=/camps/'.$camp->getId());
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
            ['href' => $this->getIriFor('materialList1')],
            ['href' => $this->getIriFor('materialList2')],
        ], $response->toArray()['_links']['items']);
    }
}
