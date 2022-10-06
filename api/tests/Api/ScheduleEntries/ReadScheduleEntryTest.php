<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadScheduleEntryTest extends ECampApiTestCase {
    public function testGetSingleScheduleEntryIsDeniedForAnonymousUser() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createBasicClient()->request('GET', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleScheduleEntryIsDeniedForUnrelatedUser() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleScheduleEntryIsDeniedForInactiveCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleScheduleEntryIsAllowedForGuest() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1'];

        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('GET', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),

            'left' => 0,
            'width' => 1,
            'dayNumber' => 2,
            'scheduleEntryNumber' => 1,
            'number' => '2.1',
            'start' => '2023-05-01T08:00:00+00:00',
            'end' => '2023-05-01T09:00:00+00:00',
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
                'period' => ['href' => $this->getIriFor('period1')],
                'day' => ['href' => $this->getIriFor('day1period1')],
            ],
        ]);
    }

    public function testGetSingleScheduleEntryIsAllowedForMember() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1'];

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('GET', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),
            'left' => 0,
            'width' => 1,
            'dayNumber' => 2,
            'scheduleEntryNumber' => 1,
            'number' => '2.1',
            'start' => '2023-05-01T08:00:00+00:00',
            'end' => '2023-05-01T09:00:00+00:00',
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
                'period' => ['href' => $this->getIriFor('period1')],
                'day' => ['href' => $this->getIriFor('day1period1')],
            ],
        ]);
    }

    public function testGetSingleScheduleEntryIsAllowedForManager() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1'];

        static::createClientWithCredentials()->request('GET', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),
            'left' => 0,
            'width' => 1,
            'dayNumber' => 2,
            'scheduleEntryNumber' => 1,
            'number' => '2.1',
            'start' => '2023-05-01T08:00:00+00:00',
            'end' => '2023-05-01T09:00:00+00:00',
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
                'period' => ['href' => $this->getIriFor('period1')],
                'day' => ['href' => $this->getIriFor('day1period1')],
            ],
        ]);
    }

    public function testGetSingleScheduleEntryInCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1period1campPrototype'];

        static::createClientWithCredentials()->request('GET', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),
            'left' => 0,
            'width' => 1,
            'dayNumber' => 1,
            'scheduleEntryNumber' => 1,
            'number' => '1.1',
            'start' => '2021-01-01T11:00:00+00:00',
            'end' => '2021-01-01T12:00:00+00:00',
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1campPrototype')],
                'period' => ['href' => $this->getIriFor('period1campPrototype')],
                'day' => ['href' => $this->getIriFor('day1period1campPrototype')],
            ],
        ]);
    }
}
