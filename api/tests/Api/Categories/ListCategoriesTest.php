<?php

namespace App\Tests\Api\Categories;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListCategoriesTest extends ECampApiTestCase {
    public function testListCategoriesIsDeniedForAnonymousUser() {
        $response = static::createBasicClient()->request('GET', '/categories');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListCategoriesIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is a category that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['category1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/categories');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 5,
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
            ['href' => $this->getIriFor('categoryWithNoActivities')],
            ['href' => $this->getIriFor('category1camp2')],
            ['href' => $this->getIriFor('category1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCategoriesFilteredByCampIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/categories?camp=%2Fcamps%2F'.$camp->getId());
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
            ['href' => $this->getIriFor('categoryWithNoActivities')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCategoriesFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/categories?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListCategoriesFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/categories?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListCategoriesFilteredByCampPrototypeIsAllowedForUnrelatedUser() {
        $camp = static::getFixture('campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/categories?camp=%2Fcamps%2F'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('category1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCategoriesAsCampSubresourceIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId().'/categories');
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
            ['href' => $this->getIriFor('categoryWithNoActivities')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCategoriesAsCampSubresourceIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/camps/'.$camp->getId().'/categories')
        ;

        $this->assertResponseStatusCodeSame(404);
    }
}
