<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListScheduleEntriesTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListScheduleEntriesIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries');
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
}
