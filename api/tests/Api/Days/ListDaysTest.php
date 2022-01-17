<?php

namespace App\Tests\Api\Days;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListDaysTest extends ECampApiTestCase {
    public function testListDaysIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/days');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListDaysIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is a day that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['day1period1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/days');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 6,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('day1period1')],
            ['href' => $this->getIriFor('day2period1')],
            ['href' => $this->getIriFor('day3period1')],
            ['href' => $this->getIriFor('day1period2')],
            ['href' => $this->getIriFor('day1period1camp2')],
            ['href' => $this->getIriFor('day1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDaysFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials()->request('GET', '/days?period=/periods/'.$period->getId());
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
            ['href' => $this->getIriFor('day1period1')],
            ['href' => $this->getIriFor('day2period1')],
            ['href' => $this->getIriFor('day3period1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDaysFilteredByPeriodIsDeniedForUnrelatedUser() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('GET', '/days?period=/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListDaysFilteredByPeriodIsDeniedForInactiveCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('GET', '/days?period=/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListDaysFilteredByPeriodInCampPrototypeIsAllowedForCollaborator() {
        $period = static::$fixtures['period1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/days?period=/periods/'.$period->getId());
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
            ['href' => $this->getIriFor('day1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
