<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;
use DateTime;

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
            'totalItems' => 4,
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
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId());
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
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByPeriodIsDeniedForInactiveCollaborator() {
        $period = static::$fixtures['period1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByPeriodInCampPrototypeIsAllowedForUnrelatedUser() {
        $period = static::$fixtures['period1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId());
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
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByActivityIsAllowedForCollaborator() {
        $activity = static::$fixtures['activity1'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?activity=%2Factivities%2F'.$activity->getId());
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
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('GET', '/schedule_entries?activity=%2Factivities%2F'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByActivityIsDeniedForInactiveCollaborator() {
        $activity = static::$fixtures['activity1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('GET', '/schedule_entries?activity=%2Factivities%2F'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByActivityInCampPrototypeIsAllowedForUnrelatedUser() {
        $activity = static::$fixtures['activity1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?activity=%2Factivities%2F'.$activity->getId());
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
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByStartBeforeIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry2period1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?start[before]='.urlencode($scheduleEntry->getStart()->format(DateTime::W3C)));
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
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByStartStrictlyBeforeIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry2period1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?start[strictly_before]='.urlencode($scheduleEntry->getStart()->format(DateTime::W3C)));
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

    public function testListScheduleEntriesFilteredByStartAfterIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1period1camp2'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?start[after]='.urlencode($scheduleEntry->getStart()->format(DateTime::W3C)));
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
            ['href' => $this->getIriFor('scheduleEntry1')],
            ['href' => $this->getIriFor('scheduleEntry1period1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByStartStrictlyAfterIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1period1camp2'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?start[strictly_after]='.urlencode($scheduleEntry->getStart()->format(DateTime::W3C)));
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

    public function testListScheduleEntriesFilteredByInvalidStartDoesntFilter() {
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?start[after]=when-I-was-young');
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
            ['href' => $this->getIriFor('scheduleEntry1')],
            ['href' => $this->getIriFor('scheduleEntry1period1camp2')],
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByEndBeforeIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry2period1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?end[before]='.urlencode($scheduleEntry->getEnd()->format(DateTime::W3C)));
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
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByEndStrictlyBeforeIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry2period1campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?end[strictly_before]='.urlencode($scheduleEntry->getEnd()->format(DateTime::W3C)));
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

    public function testListScheduleEntriesFilteredByEndAfterIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1period1camp2'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?end[after]='.urlencode($scheduleEntry->getEnd()->format(DateTime::W3C)));
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
            ['href' => $this->getIriFor('scheduleEntry1')],
            ['href' => $this->getIriFor('scheduleEntry1period1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByEndStrictlyAfterIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1period1camp2'];
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?end[strictly_after]='.urlencode($scheduleEntry->getEnd()->format(DateTime::W3C)));
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

    public function testListScheduleEntriesFilteredByInvalidEndDoesntFilter() {
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?end[before]=when-I-was-young');
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
            ['href' => $this->getIriFor('scheduleEntry1')],
            ['href' => $this->getIriFor('scheduleEntry1period1camp2')],
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
