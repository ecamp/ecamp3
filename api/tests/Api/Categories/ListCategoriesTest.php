<?php

namespace App\Tests\Api\Categories;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListCategoriesTest extends ECampApiTestCase {
    public function testListCategoriesIsDeniedToAnonymousUser() {
        $response = static::createClient()->request('GET', '/categories');
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
            'totalItems' => 4,
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
            ['href' => $this->getIriFor('category1campPrototype')],
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

    public function testListCategoriesFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/categories?camp=/camps/'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertStringNotContainsString($this->getIriFor('category1'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('category2'), $response->getContent());
    }

    public function testListCategoriesFilteredByPrototypeCampIsAllowedForUnrelatedUser() {
        $camp = static::$fixtures['campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/categories?camp=/camps/'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('category1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
