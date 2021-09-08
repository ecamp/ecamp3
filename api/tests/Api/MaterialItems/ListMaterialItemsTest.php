<?php

namespace App\Tests\Api\MaterialItems;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListMaterialItemsTest extends ECampApiTestCase {
    public function testListMaterialItemsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/material_items');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListMaterialItemsIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is a material item that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['materialItem1period1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/material_items');
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
            ['href' => $this->getIriFor('materialItem1')],
            ['href' => $this->getIriFor('materialItem1period1')],
            ['href' => $this->getIriFor('materialItem1period1camp2')],
            ['href' => $this->getIriFor('materialItem1period1campPrototype')],
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

    public function testListMaterialItemsFilteredByMaterialListIsDeniedForUnrelatedUser() {
        $materialList = static::$fixtures['materialList1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/material_items?materialList=/material_lists/'.$materialList->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialItemsFilteredByMaterialListIsDeniedForInactiveCollaborator() {
        $materialList = static::$fixtures['materialList1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/material_items?materialList=/material_lists/'.$materialList->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialItemsFilteredByMaterialListInCampPrototypeIsAllowedForUnrelatedUser() {
        $materialList = static::$fixtures['materialList1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/material_items?materialList=/material_lists/'.$materialList->getId());
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
            ['href' => $this->getIriFor('materialItem1period1campPrototype')],
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

    public function testListMaterialItemsFilteredByPeriodIsDeniedForUnrelatedUser() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/material_items?period=/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialItemsFilteredByPeriodIsDeniedForInactiveCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/material_items?period=/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialItemsFilteredByPeriodInCampPrototypeIsAllowedForUnrelatedUser() {
        $period = static::$fixtures['period1campPrototype'];
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
            ['href' => $this->getIriFor('materialItem1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
