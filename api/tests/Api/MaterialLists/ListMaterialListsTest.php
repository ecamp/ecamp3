<?php

namespace App\Tests\Api\MaterialLists;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListMaterialListsTest extends ECampApiTestCase {
    public function testListMaterialListsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/material_lists');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListMaterialListsIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is a material list that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['materialList1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/material_lists');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 4,
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
            ['href' => $this->getIriFor('materialList1campPrototype')],
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

    public function testListMaterialListsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('GET', '/material_lists?camp=/camps/'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialListsFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('GET', '/material_lists?camp=/camps/'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialListsFilteredByCampPrototypeIsAllowedForUnrelatedUser() {
        $camp = static::$fixtures['campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/material_lists?camp=/camps/'.$camp->getId());
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
            ['href' => $this->getIriFor('materialList1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
