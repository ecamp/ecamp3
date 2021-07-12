<?php

namespace App\Tests\Api\MaterialItems;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListMaterialItemsTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListMaterialItemsIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/material_items');
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
            ['href' => $this->getIriFor('materialItem1')],
            ['href' => $this->getIriFor('materialItem1period1')],
            ['href' => $this->getIriFor('materialItem1period1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListMaterialItemsFilteredByMaterialListIsAllowedForCollaborator() {
        $materialList = static::$fixtures['materialList1'];
        $response = static::createClientWithCredentials()->request('GET', '/material_items?materialList=/material_lists/'.$materialList->getId());
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
            ['href' => $this->getIriFor('materialItem1')],
            ['href' => $this->getIriFor('materialItem1period1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListMaterialItemsFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials()->request('GET', '/material_items?period=/periods/'.$period->getId());
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
            ['href' => $this->getIriFor('materialItem1period1')],
        ], $response->toArray()['_links']['items']);
    }
}
