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

    public function testListDayResponsiblesWithoutFilterIsNotAllowedForLoggedInUser() {
        // precondition: There is a day responsible that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['dayResponsible1day1period1campUnrelated']);

        static::createClientWithCredentials()->request('GET', '/day_responsibles');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListDayResponsiblesFilteredByDayIsAllowedForCollaborator() {
        $day = static::getFixture('day1period1');
        $response = static::createClientWithCredentials()->request('GET', '/day_responsibles?day=%2Fdays%2F'.$day->getId());
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
        $day = static::getFixture('day1period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/day_responsibles?day=%2Fdays%2F'.$day->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListDayResponsiblesFilteredByDayIsDeniedForInactiveCollaborator() {
        $day = static::getFixture('day1period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/day_responsibles?day=%2Fdays%2F'.$day->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListDayResponsiblesFilteredByDayInCampPrototypeIsAllowedForUnrelatedUser() {
        $day = static::getFixture('day1period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/day_responsibles?day=%2Fdays%2F'.$day->getId());
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

    public function testListDayResponsiblesFilteredByDayInSharedCampIsAllowedForUnrelatedUser() {
        $day = static::getFixture('day1period1campShared');
        $response = static::createClientWithCredentials()->request('GET', '/day_responsibles?day=%2Fdays%2F'.$day->getId());
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
            ['href' => $this->getIriFor('dayResponsible1day1period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDayResponsiblesFilteredByDayInSharedCampIsAllowedForInactiveUser() {
        $day = static::getFixture('day1period1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/day_responsibles?day=%2Fdays%2F'.$day->getId())
        ;
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
            ['href' => $this->getIriFor('dayResponsible1day1period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDayResponsiblesFilteredByDayInSharedCampIsAllowedForInvitedUser() {
        $day = static::getFixture('day1period1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('GET', '/day_responsibles?day=%2Fdays%2F'.$day->getId())
        ;
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
            ['href' => $this->getIriFor('dayResponsible1day1period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDayResponsiblesAsDaySubresourceIsAllowedForCollaborator() {
        $day = static::getFixture('day1period1');
        $response = static::createClientWithCredentials()->request('GET', '/days/'.$day->getId().'/day_responsibles');
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

    public function testListDayResponsiblesAsDaySubresourceIsDeniedForUnrelatedUser() {
        $day = static::getFixture('day1period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/days/'.$day->getId().'/day_responsibles')
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testListDayResponsiblesFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()
            ->request('GET', '/day_responsibles?day.period=%2Fperiods%2F'.$period->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 2]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('dayResponsible1')],
            ['href' => $this->getIriFor('dayResponsible1day2period1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDayResponsiblesFilteredByPeriodIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/day_responsibles?day.period=%2Fperiods%2F'.$period->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }
}
