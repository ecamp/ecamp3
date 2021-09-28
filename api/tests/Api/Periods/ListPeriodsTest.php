<?php

namespace App\Tests\Api\Periods;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListPeriodsTest extends ECampApiTestCase {
    public function testListPeriodsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/periods');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListPeriodsIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is a period that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['period1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/periods');
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
            ['href' => $this->getIriFor('period1')],
            ['href' => $this->getIriFor('period2')],
            ['href' => $this->getIriFor('period1camp2')],
            ['href' => $this->getIriFor('period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/periods?camp=/camps/'.$camp->getId());
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
            ['href' => $this->getIriFor('period1')],
            ['href' => $this->getIriFor('period2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/periods?camp=/camps/'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListPeriodsFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/periods?camp=/camps/'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListPeriodsFilteredByCampPrototypeIsAllowedForUnrelatedUser() {
        $camp = static::$fixtures['campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/periods?camp=/camps/'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
