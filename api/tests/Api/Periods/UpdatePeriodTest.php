<?php

namespace App\Tests\Api\Periods;

use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdatePeriodTest extends ECampApiTestCase {
    // TODO validation tests
    // TODO moving a period vs changing the time window

    public function testPatchPeriodIsDeniedForAnonymousUser() {
        $period = static::$fixtures['period1'];
        static::createBasicClient()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => 'Vorweekend',
            'start' => '2023-01-01',
            'end' => '2023-01-02',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchPeriodIsDeniedForUnrelatedUser() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'description' => 'Vorweekend',
                'start' => '2023-01-01',
                'end' => '2023-01-02',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchPeriodIsDeniedForInactiveCollaborator() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'description' => 'Vorweekend',
                'start' => '2023-01-01',
                'end' => '2023-01-02',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchPeriodIsDeniedForGuest() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'description' => 'Vorweekend',
                'start' => '2023-01-01',
                'end' => '2023-01-02',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchPeriodIsAllowedForMember() {
        $period = static::$fixtures['period1'];

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'description' => 'Vorweekend',
                'start' => '2023-01-01',
                'end' => '2023-01-02',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'description' => 'Vorweekend',
            'start' => '2023-01-01',
            'end' => '2023-01-02',
        ]);
    }

    public function testPatchPeriodIsAllowedForManager() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => 'Vorweekend',
            'start' => '2023-01-01',
            'end' => '2023-01-02',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'description' => 'Vorweekend',
            'start' => '2023-01-01',
            'end' => '2023-01-02',
        ]);
    }

    public function testPatchPeriodInCampPrototypeIsDeniedForUnrelatedUser() {
        $period = static::$fixtures['period1campPrototype'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => 'Vorweekend',
            'start' => '2023-01-01',
            'end' => '2023-01-02',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchPeriodDisallowsChangingCamp() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'camp' => $this->getIriFor('camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" is unknown).',
        ]);
    }

    public function testPatchPeriodValidatesNullDescription() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'description',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchPeriodValidatesEmptyDescription() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'description',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchPeriodValidatesTooLongDescription() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => str_repeat('l', 33),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'description',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchPeriodTrimsDescription() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => " \t Vorweekend \t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'description' => 'Vorweekend',
        ]);
    }

    public function testPatchPeriodCleansHtmlForDescription() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => 'Vorwe<script>alert(1)</script>ekend',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'description' => 'Vorweekend',
        ]);
    }

    public function testPatchPeriodValidatesInvalidStart() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'start' => 'something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Parsing datetime string "something" using format "!Y-m-d" resulted in 3 errors: 
at position 0: A four digit year could not be found
at position 9: Not enough data available to satisfy format',
        ]);
    }

    public function testPatchPeriodValidatesInvalidEnd() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'end' => 'something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Parsing datetime string "something" using format "!Y-m-d" resulted in 3 errors: 
at position 0: A four digit year could not be found
at position 9: Not enough data available to satisfy format',
        ]);
    }

    public function testPatchPeriodValidatesStartAfterEnd() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'start' => '2021-01-10',
            'end' => '2021-01-09',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'start',
                    'message' => 'This value should be less than or equal to Jan 9, 2021, 12:00 AM.',
                ],
                [
                    'propertyPath' => 'end',
                    'message' => 'This value should be greater than or equal to Jan 10, 2021, 12:00 AM.',
                ],
            ],
        ]);
    }

    public function testPatchPeriodReturnsProperDatesInTimezoneAheadOfUTC() {
        $period = static::$fixtures['period1'];
        date_default_timezone_set('Asia/Singapore');

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-01-01',
                'end' => '2023-01-02',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'start' => '2023-01-01',
            'end' => '2023-01-02',
        ]);
    }

    public function testPatchPeriodReturnsProperDatesInTimezoneBehindUTC() {
        $period = static::$fixtures['period1'];
        date_default_timezone_set('America/New_York');

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-01-01',
                'end' => '2023-01-02',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'start' => '2023-01-01',
            'end' => '2023-01-02',
        ]);
    }

    public function testPatchPeriodAddDays() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        $this->assertEquals(3, $period->getPeriodLength());

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-01-01',
                'end' => '2023-01-04',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);

        /** @var Period $period */
        $period = $this->getEntityManager()->getRepository(Period::class)->find($period->getId());
        $this->assertCount(4, $period->days);
    }

    public function testPatchPeriodRemoveDays() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        $this->assertEquals(3, $period->getPeriodLength());

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-01-01',
                'end' => '2023-01-02',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);

        /** @var Period $period */
        $period = $this->getEntityManager()->getRepository(Period::class)->find($period->getId());
        $this->assertCount(2, $period->days);
    }

    public function testPatchPeriodMovesScheduleEntries() {
        /** @var Period $period */
        $period = static::$fixtures['period1camp2'];

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-03-24',
                'end' => '2023-03-26',
                'moveScheduleEntries' => true,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);

        /** @var Period $period */
        $period = $this->getEntityManager()->getRepository(Period::class)->find($period->getId());
        $this->assertCount(1, $period->scheduleEntries);

        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = $period->scheduleEntries[0];
        $this->assertEquals(540, $scheduleEntry->startOffset);
        $this->assertEquals(600, $scheduleEntry->endOffset);
    }

    public function testPatchPeriodDoNotMoveScheduleEntries() {
        /** @var Period $period */
        $period = static::$fixtures['period1camp2'];

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-03-24',
                'end' => '2023-03-26',
                'moveScheduleEntries' => false,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);

        /** @var Period $period */
        $period = $this->getEntityManager()->getRepository(Period::class)->find($period->getId());
        $this->assertCount(1, $period->scheduleEntries);

        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = $period->scheduleEntries[0];
        $this->assertEquals(1440 + 540, $scheduleEntry->startOffset);
        $this->assertEquals(1440 + 600, $scheduleEntry->endOffset);
    }

    public function testPatchPeriodValidateStartDate() {
        /** @var Period $period */
        $period = static::$fixtures['period1camp2'];

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-03-26',
                'end' => '2023-03-27',
                'moveScheduleEntries' => false,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'start',
                    'message' => 'Due to existing schedule entries, start-date can not be later then 2023-03-25',
                ],
            ],
        ]);
    }

    public function testPatchPeriodValidateEndDate() {
        /** @var Period $period */
        $period = static::$fixtures['period1camp2'];

        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-03-24',
                'end' => '2023-03-24',
                'moveScheduleEntries' => false,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'end',
                    'message' => 'Due to existing schedule entries, end-date can not be earlier then 2023-03-25',
                ],
            ],
        ]);
    }
}
