<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListScheduleEntriesTest extends ECampApiTestCase {
    public function testListScheduleEntriesIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/schedule_entries');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListScheduleEntriesIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is a schedule entry that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['scheduleEntry1period1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries');
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
            ['href' => $this->getIriFor('scheduleEntry1')],
            ['href' => $this->getIriFor('scheduleEntry1period1camp2')],
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?period=/periods/'.$period->getId());
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
            ['href' => $this->getIriFor('scheduleEntry1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByPeriodIsDeniedForUnrelatedUser() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/schedule_entries?period=/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByPeriodIsDeniedForInactiveCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/schedule_entries?period=/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByPeriodInCampPrototypeIsAllowedForUnrelatedUser() {
        $period = static::$fixtures['period1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?period=/periods/'.$period->getId());
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
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByActivityIsAllowedForCollaborator() {
        $activity = static::$fixtures['activity1'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?activity=/activities/'.$activity->getId());
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
            ['href' => $this->getIriFor('scheduleEntry1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByActivityIsDeniedForUnrelatedUser() {
        $activity = static::$fixtures['activity1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/schedule_entries?activity=/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByActivityIsDeniedForInactiveCollaborator() {
        $activity = static::$fixtures['activity1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/schedule_entries?activity=/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByActivityInCampPrototypeIsAllowedForUnrelatedUser() {
        $activity = static::$fixtures['activity1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?activity=/activities/'.$activity->getId());
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
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
