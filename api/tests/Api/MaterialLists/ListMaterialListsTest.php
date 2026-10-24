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

    public function testListMaterialListsWithoutFilterIsNotAllowedForLoggedInUser() {
        // precondition: There is a material list that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['materialList1campUnrelated']);

        static::createClientWithCredentials()->request('GET', '/material_lists');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListMaterialListsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/material_lists?camp=%2Fcamps%2F'.$camp->getId());
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
            ['href' => $this->getIriFor('materialList2WithNoItems')],
            ['href' => $this->getIriFor('materialList3Manager')],
            ['href' => $this->getIriFor('materialList4Member')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListMaterialListsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/material_lists?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialListsFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/material_lists?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListMaterialListsFilteredByCampPrototypeIsAllowedForUnrelatedUser() {
        $camp = static::getFixture('campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/material_lists?camp=%2Fcamps%2F'.$camp->getId());
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

    public function testListMaterialListsFilteredByCampIsAllowedForUnrelatedUser() {
        $camp = static::getFixture('campShared');
        $response = static::createClientWithCredentials()->request('GET', '/material_lists?camp=%2Fcamps%2F'.$camp->getId());
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
            ['href' => $this->getIriFor('materialList1campShared')],
            ['href' => $this->getIriFor('materialList2campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListMaterialListsFilteredByCampIsAllowedForInactiveUser() {
        $camp = static::getFixture('campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/material_lists?camp=%2Fcamps%2F'.$camp->getId())
        ;
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
            ['href' => $this->getIriFor('materialList1campShared')],
            ['href' => $this->getIriFor('materialList2campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListMaterialListsFilteredByCampIsAllowedForInvitedUser() {
        $camp = static::getFixture('campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('GET', '/material_lists?camp=%2Fcamps%2F'.$camp->getId())
        ;
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
            ['href' => $this->getIriFor('materialList1campShared')],
            ['href' => $this->getIriFor('materialList2campShared')],
        ], $response->toArray()['_links']['items']);
    }
}
