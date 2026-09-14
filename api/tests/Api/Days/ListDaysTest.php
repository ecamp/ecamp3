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

    public function testListDaysWithoutFilterIsNotAllowedForLoggedInUser() {
        // precondition: There is a day that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['day1period1campUnrelated']);

        static::createClientWithCredentials()->request('GET', '/days');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListDaysFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()->request('GET', '/days?period=%2Fperiods%2F'.$period->getId());
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
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/days?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListDaysFilteredByPeriodIsDeniedForInactiveCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/days?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListDaysOrdersByDate() {
        $camp = static::getFixture('camp1');
        $client = static::createClientWithCredentials();
        $response = $client->request('GET', '/days?period.camp=%2Fcamps%2F'.$camp->getId());
        $this->assertEquals([
            ['href' => $this->getIriFor('day1period2')],
            ['href' => $this->getIriFor('day2period2')],
            ['href' => $this->getIriFor('day3period2')],
            ['href' => $this->getIriFor('day1period1')],
            ['href' => $this->getIriFor('day2period1')],
            ['href' => $this->getIriFor('day3period1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDaysFilteredByPeriodInCampPrototypeIsAllowedForUnrelatedUser() {
        $period = static::getFixture('period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/days?period=%2Fperiods%2F'.$period->getId());
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

    public function testListDaysFilteredByPeriodInSharedCampIsAllowedForUnrelatedUser() {
        $period = static::getFixture('period1campShared');
        $response = static::createClientWithCredentials()->request('GET', '/days?period=%2Fperiods%2F'.$period->getId());
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
            ['href' => $this->getIriFor('day1period1campShared')],
            ['href' => $this->getIriFor('day2period1campShared')],
            ['href' => $this->getIriFor('day3period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDaysFilteredByPeriodInSharedCampIsAllowedForInactiveUser() {
        $period = static::getFixture('period1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/days?period=%2Fperiods%2F'.$period->getId())
        ;
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
            ['href' => $this->getIriFor('day1period1campShared')],
            ['href' => $this->getIriFor('day2period1campShared')],
            ['href' => $this->getIriFor('day3period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDaysFilteredByPeriodInSharedCampIsAllowedForInvitedUser() {
        $period = static::getFixture('period1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('GET', '/days?period=%2Fperiods%2F'.$period->getId())
        ;
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
            ['href' => $this->getIriFor('day1period1campShared')],
            ['href' => $this->getIriFor('day2period1campShared')],
            ['href' => $this->getIriFor('day3period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListDaysAsPeriodSubresourceIsAllowedForCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()->request('GET', '/periods/'.$period->getId().'/days');
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

    public function testListDaysAsPeriodSubresourceIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/periods/'.$period->getId().'/days')
        ;

        $this->assertResponseStatusCodeSame(404);
    }
}
