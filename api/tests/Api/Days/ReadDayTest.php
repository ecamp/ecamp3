<?php

namespace App\Tests\Api\Days;

use App\Entity\Day;
use App\Tests\Api\ECampApiTestCase;
use DateTime;

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
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
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
        $start = $day->getStart()->format(DateTime::W3C);
        $end = $day->getEnd()->format(DateTime::W3C);
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('GET', '/days/'.$day->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => 2,
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                'scheduleEntries' => ['href' => '/schedule_entries?period=%2Fperiods%2F'.$day->period->getId().'&start%5Bstrictly_before%5D='.urlencode($end).'&end%5Bafter%5D='.urlencode($start)],
                'dayResponsibles' => ['href' => '/day_responsibles?day=/days/'.$day->getId()],
            ],
        ]);
    }

    public function testGetSingleDayIsAllowedForMember() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        $start = $day->getStart()->format(DateTime::W3C);
        $end = $day->getEnd()->format(DateTime::W3C);
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('GET', '/days/'.$day->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => 2,
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                'scheduleEntries' => ['href' => '/schedule_entries?period=%2Fperiods%2F'.$day->period->getId().'&start%5Bstrictly_before%5D='.urlencode($end).'&end%5Bafter%5D='.urlencode($start)],
                'dayResponsibles' => ['href' => '/day_responsibles?day=/days/'.$day->getId()],
            ],
        ]);
    }

    public function testGetSingleDayIsAllowedForManager() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        $start = $day->getStart()->format(DateTime::W3C);
        $end = $day->getEnd()->format(DateTime::W3C);
        static::createClientWithCredentials()->request('GET', '/days/'.$day->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => 2,
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                'scheduleEntries' => ['href' => '/schedule_entries?period=%2Fperiods%2F'.$day->period->getId().'&start%5Bstrictly_before%5D='.urlencode($end).'&end%5Bafter%5D='.urlencode($start)],
                'dayResponsibles' => ['href' => '/day_responsibles?day=/days/'.$day->getId()],
            ],
        ]);
    }

    public function testGetSingleDayFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1campPrototype'];
        $start = $day->getStart()->format(DateTime::W3C);
        $end = $day->getEnd()->format(DateTime::W3C);
        static::createClientWithCredentials()->request('GET', '/days/'.$day->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => $day->getDayNumber(),
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1campPrototype')],
                'scheduleEntries' => ['href' => '/schedule_entries?period=%2Fperiods%2F'.$day->period->getId().'&start%5Bstrictly_before%5D='.urlencode($end).'&end%5Bafter%5D='.urlencode($start)],
                'dayResponsibles' => ['href' => '/day_responsibles?day=/days/'.$day->getId()],
            ],
        ]);
    }

    public function testDatesFormatProperlyInTimezoneAheadOfUTC() {
        // given
        date_default_timezone_set('Asia/Singapore');

        /** @var Day $day */
        $day = static::$fixtures['day1period1'];

        // when
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('GET', '/days/'.$day->getId())
        ;

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'start' => '2023-05-01T00:00:00+00:00',
            'end' => '2023-05-02T00:00:00+00:00',
        ]);
    }

    public function testDatesFormatProperlyInTimezoneBehindUTC() {
        // given
        date_default_timezone_set('America/New_York');

        /** @var Day $day */
        $day = static::$fixtures['day1period1'];

        // when
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('GET', '/days/'.$day->getId())
        ;

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'start' => '2023-05-01T00:00:00+00:00',
            'end' => '2023-05-02T00:00:00+00:00',
        ]);
    }
}
