<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
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

    public function testListScheduleEntriesWithoutFilterIsNotAllowedForLoggedInUser() {
        // precondition: There is a schedule entry that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['scheduleEntry1period1campUnrelated']);

        static::createClientWithCredentials()->request('GET', '/schedule_entries');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListScheduleEntriesFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId());
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
            ['href' => $this->getIriFor('scheduleEntry2')],
            ['href' => $this->getIriFor('scheduleEntry1period1camp1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByPeriodIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByPeriodIsDeniedForInactiveCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByPeriodInCampPrototypeIsAllowedForUnrelatedUser() {
        $period = static::getFixture('period1campPrototype');
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

    public function testListScheduleEntriesFilteredByPeriodInSharedCampIsAllowedForUnrelatedUser() {
        $period = static::getFixture('period1campShared');
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
            ['href' => $this->getIriFor('scheduleEntry1period1campShared')],
            ['href' => $this->getIriFor('scheduleEntry2period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByPeriodInSharedCampIsAllowedForInactiveUser() {
        $period = static::getFixture('period1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId())
        ;
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
            ['href' => $this->getIriFor('scheduleEntry1period1campShared')],
            ['href' => $this->getIriFor('scheduleEntry2period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByPeriodInSharedCampIsAllowedForInvitedUser() {
        $period = static::getFixture('period1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('GET', '/schedule_entries?period=%2Fperiods%2F'.$period->getId())
        ;
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
            ['href' => $this->getIriFor('scheduleEntry1period1campShared')],
            ['href' => $this->getIriFor('scheduleEntry2period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByActivityIsAllowedForCollaborator() {
        $activity = static::getFixture('activity1');
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
            ['href' => $this->getIriFor('scheduleEntry1')],
            ['href' => $this->getIriFor('scheduleEntry2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByActivityIsDeniedForUnrelatedUser() {
        $activity = static::getFixture('activity1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/schedule_entries?activity=%2Factivities%2F'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByActivityIsDeniedForInactiveCollaborator() {
        $activity = static::getFixture('activity1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/schedule_entries?activity=%2Factivities%2F'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListScheduleEntriesFilteredByActivityInCampPrototypeIsAllowedForUnrelatedUser() {
        $activity = static::getFixture('activity1campPrototype');
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

    public function testListScheduleEntriesFilteredByActivityInSharedCampIsAllowedForUnrelatedUser() {
        $activity = static::getFixture('activity1campShared');
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
            ['href' => $this->getIriFor('scheduleEntry1period1campShared')],
            ['href' => $this->getIriFor('scheduleEntry2period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByActivityInSharedCampIsAllowedForInactiveUser() {
        $activity = static::getFixture('activity1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/schedule_entries?activity=%2Factivities%2F'.$activity->getId())
        ;
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
            ['href' => $this->getIriFor('scheduleEntry1period1campShared')],
            ['href' => $this->getIriFor('scheduleEntry2period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByActivityInSharedCampIsAllowedForInvitedUser() {
        $activity = static::getFixture('activity1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('GET', '/schedule_entries?activity=%2Factivities%2F'.$activity->getId())
        ;
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
            ['href' => $this->getIriFor('scheduleEntry1period1campShared')],
            ['href' => $this->getIriFor('scheduleEntry2period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByStartBeforeIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::getFixture('scheduleEntry2period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&start[before]='.urlencode($scheduleEntry->getStart()->format(\DateTime::W3C)));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 2]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByStartStrictlyBeforeIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::getFixture('scheduleEntry2period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&start[strictly_before]='.urlencode($scheduleEntry->getStart()->format(\DateTime::W3C)));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByStartAfterIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::getFixture('scheduleEntry1period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&start[after]='.urlencode($scheduleEntry->getStart()->format(\DateTime::W3C)));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 2]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByStartStrictlyAfterIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::getFixture('scheduleEntry1period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&start[strictly_after]='.urlencode($scheduleEntry->getStart()->format(\DateTime::W3C)));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByInvalidStartDoesntFilter() {
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&start[after]=when-I-was-young');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 2]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByEndBeforeIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::getFixture('scheduleEntry2period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&end[before]='.urlencode($scheduleEntry->getEnd()->format(\DateTime::W3C)));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 2]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByEndStrictlyBeforeIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::getFixture('scheduleEntry2period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&end[strictly_before]='.urlencode($scheduleEntry->getEnd()->format(\DateTime::W3C)));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByEndAfterIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::getFixture('scheduleEntry1period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&end[after]='.urlencode($scheduleEntry->getEnd()->format(\DateTime::W3C)));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 2]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByEndStrictlyAfterIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::getFixture('scheduleEntry1period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&end[strictly_after]='.urlencode($scheduleEntry->getEnd()->format(\DateTime::W3C)));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesFilteredByInvalidEndDoesntFilter() {
        $response = static::createClientWithCredentials()->request('GET', $this->scheduleEntriesInPeriod1CampPrototype().'&end[after]=when-I-was-young');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 2]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('scheduleEntry1period1campPrototype')],
            ['href' => $this->getIriFor('scheduleEntry2period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesAsPeriodSubresourceIsAllowedForCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()->request('GET', '/periods/'.$period->getId().'/schedule_entries');
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
            ['href' => $this->getIriFor('scheduleEntry2')],
            ['href' => $this->getIriFor('scheduleEntry1period1camp1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListScheduleEntriesAsPeriodSubresourceIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/periods/'.$period->getId().'/schedule_entries')
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    private function scheduleEntriesInPeriod1CampPrototype(): string {
        return '/schedule_entries?period='.urlencode($this->getIriFor('period1campPrototype'));
    }
}
