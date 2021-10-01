<?php

namespace App\Tests\Api\Days;

use App\Entity\Day;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadDayTest extends ECampApiTestCase {
    public function testGetSingleDayIsDeniedForAnonymousUser() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        static::createBasicClient()->request('GET', '/days/'.$day->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleDayIsDeniedForUnrelatedUser() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/days/'.$day->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleDayIsDeniedForInactiveCollaborator() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/days/'.$day->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleDayIsAllowedForGuest() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('GET', '/days/'.$day->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => $day->getDayNumber(),
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                //'scheduleEntries' => ['href' => '/schedule_entries?day=/days/'.$day->getId()],
                'dayResponsibles' => ['href' => '/day_responsibles?day=/days/'.$day->getId()],
            ],
        ]);
    }

    public function testGetSingleDayIsAllowedForMember() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('GET', '/days/'.$day->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => $day->getDayNumber(),
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                //'scheduleEntries' => ['href' => '/schedule_entries?day=/days/'.$day->getId()],
                'dayResponsibles' => ['href' => '/day_responsibles?day=/days/'.$day->getId()],
            ],
        ]);
    }

    public function testGetSingleDayIsAllowedForManager() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        static::createClientWithCredentials()->request('GET', '/days/'.$day->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => $day->getDayNumber(),
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                //'scheduleEntries' => ['href' => '/schedule_entries?day=/days/'.$day->getId()],
                'dayResponsibles' => ['href' => '/day_responsibles?day=/days/'.$day->getId()],
            ],
        ]);
    }

    public function testGetSingleDayFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1campPrototype'];
        static::createClientWithCredentials()->request('GET', '/days/'.$day->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => $day->getDayNumber(),
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1campPrototype')],
                //'scheduleEntries' => ['href' => '/schedule_entries?day=/days/'.$day->getId()],
            ],
        ]);
    }
}
