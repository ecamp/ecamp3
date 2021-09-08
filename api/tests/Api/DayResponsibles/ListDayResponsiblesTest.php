<?php

namespace App\Tests\Api\DayResponsibles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListDayResponsiblesTest extends ECampApiTestCase {
    public function testListDayResponsiblesIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/day_responsibles');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListDayResponsiblesIsAllowedForCollaboratorButFiltered() {
        // precondition: There is a day responsible that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['dayResponsible1day1period1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/day_responsibles');
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
            ['href' => $this->getIriFor('dayResponsible1')],
            ['href' => $this->getIriFor('dayResponsible1day2period1')],
            ['href' => $this->getIriFor('dayResponsible1day1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDayResponsiblesFilteredByDayIsAllowedForCollaborator() {
        $day = static::$fixtures['day1period1'];
        $response = static::createClientWithCredentials()->request('GET', '/day_responsibles?day=/days/'.$day->getId());
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
            ['href' => $this->getIriFor('dayResponsible1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDayResponsiblesFilteredByDayIsDeniedForUnrelatedUser() {
        $day = static::$fixtures['day1period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/day_responsibles?day=/days/'.$day->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertStringNotContainsString($this->getIriFor('dayResponsible1'), $response->getContent());
    }

    public function testListDayResponsiblesFilteredByDayIsDeniedForInactiveCollaborator() {
        $day = static::$fixtures['day1period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/day_responsibles?day=/days/'.$day->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertStringNotContainsString($this->getIriFor('dayResponsible1'), $response->getContent());
    }

    public function testListDayResponsiblesFilteredByDayInCampPrototypeIsAllowedForUnrelatedUser() {
        $day = static::$fixtures['day1period1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/day_responsibles?day=/days/'.$day->getId());
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
            ['href' => $this->getIriFor('dayResponsible1day1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
