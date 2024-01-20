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
        $materialList = static::getFixture('materialList1');
        $response = static::createClientWithCredentials()->request('GET', '/material_items?materialList=%2Fmaterial_lists%2F'.$materialList->getId());
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
        $materialList = static::getFixture('materialList1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/material_items?materialList=%2Fmaterial_lists%2F'.$materialList->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialItemsFilteredByMaterialListIsDeniedForInactiveCollaborator() {
        $materialList = static::getFixture('materialList1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/material_items?materialList=%2Fmaterial_lists%2F'.$materialList->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialItemsFilteredByMaterialListInCampPrototypeIsAllowedForUnrelatedUser() {
        $materialList = static::getFixture('materialList1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/material_items?materialList=%2Fmaterial_lists%2F'.$materialList->getId());
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
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()->request('GET', '/material_items?period=%2Fperiods%2F'.$period->getId());
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
            ['href' => $this->getIriFor('materialItem1period1')],
            ['href' => $this->getIriFor('materialItem1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListMaterialItemsFilteredByPeriodIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/material_items?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testListMaterialItemsFilteredByPeriodIsDeniedForInactiveCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/material_items?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testListMaterialItemsFilteredByPeriodInCampPrototypeIsAllowedForUnrelatedUser() {
        $period = static::getFixture('period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/material_items?period=%2Fperiods%2F'.$period->getId());
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
