<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;
use DateInterval;
use DateTime;

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
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
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
        $start = DateTime::createFromInterface($scheduleEntry->period->start)->add(new DateInterval('PT'.$scheduleEntry->periodOffset.'M'));
        $end = DateTime::createFromInterface($start)->add(new DateInterval('PT'.$scheduleEntry->length.'M'));
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('GET', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),
            'periodOffset' => $scheduleEntry->periodOffset,
            'length' => $scheduleEntry->length,
            'left' => 0,
            'width' => 1,
            'dayNumber' => 1,
            'scheduleEntryNumber' => 1,
            'number' => '1.1',
            'start' => $start->format(DateTime::W3C),
            'end' => $end->format(DateTime::W3C),
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
        $start = DateTime::createFromInterface($scheduleEntry->period->start)->add(new DateInterval('PT'.$scheduleEntry->periodOffset.'M'));
        $end = DateTime::createFromInterface($start)->add(new DateInterval('PT'.$scheduleEntry->length.'M'));
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('GET', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),
            'periodOffset' => $scheduleEntry->periodOffset,
            'length' => $scheduleEntry->length,
            'left' => 0,
            'width' => 1,
            'dayNumber' => 1,
            'scheduleEntryNumber' => 1,
            'number' => '1.1',
            'start' => $start->format(DateTime::W3C),
            'end' => $end->format(DateTime::W3C),
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
        $start = DateTime::createFromInterface($scheduleEntry->period->start)->add(new DateInterval('PT'.$scheduleEntry->periodOffset.'M'));
        $end = DateTime::createFromInterface($start)->add(new DateInterval('PT'.$scheduleEntry->length.'M'));
        static::createClientWithCredentials()->request('GET', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),
            'periodOffset' => $scheduleEntry->periodOffset,
            'length' => $scheduleEntry->length,
            'left' => 0,
            'width' => 1,
            'dayNumber' => 1,
            'scheduleEntryNumber' => 1,
            'number' => '1.1',
            'start' => $start->format(DateTime::W3C),
            'end' => $end->format(DateTime::W3C),
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
        $start = DateTime::createFromInterface($scheduleEntry->period->start)->add(new DateInterval('PT'.$scheduleEntry->periodOffset.'M'));
        $end = DateTime::createFromInterface($start)->add(new DateInterval('PT'.$scheduleEntry->length.'M'));
        static::createClientWithCredentials()->request('GET', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),
            'periodOffset' => $scheduleEntry->periodOffset,
            'length' => $scheduleEntry->length,
            'left' => 0,
            'width' => 1,
            'dayNumber' => 1,
            'scheduleEntryNumber' => 1,
            'number' => '1.1',
            'start' => $start->format(DateTime::W3C),
            'end' => $end->format(DateTime::W3C),
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1campPrototype')],
                'period' => ['href' => $this->getIriFor('period1campPrototype')],
                'day' => ['href' => $this->getIriFor('day1period1campPrototype')],
            ],
        ]);
    }
}
