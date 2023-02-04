<?php

namespace App\Tests\Api\Periods;

use App\Entity\Day;
use App\Entity\DayResponsible;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdatePeriodTest extends ECampApiTestCase {
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
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
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
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
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
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
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

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

    public function testPatchPeriodCleansForbiddenCharactersForDescription() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'description' => "Vorwe\n\tekend",
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

    public function testPatchPeriodValidatesInvalidStartFormat() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'start' => '2023-05-01+01:00',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Parsing datetime string "2023-05-01+01:00" using format "!Y-m-d" resulted in 1 errors: 
at position 10: Trailing data',
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

    public function testPatchPeriodValidatesInvalidEndFormat() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'end' => '2023-05-03T01:00',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Parsing datetime string "2023-05-03T01:00" using format "!Y-m-d" resulted in 1 errors: 
at position 10: Trailing data',
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

    public function testPatchPeriodValidatesOverlappingPeriods() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'start' => '2023-04-15',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'start',
                    'message' => 'Periods must not overlap.',
                ],
            ],
        ]);
    }

    public function testPatchPeriodReturnsProperDatesInTimezoneAheadOfUTC() {
        $period = static::$fixtures['period1'];
        date_default_timezone_set('Asia/Singapore');

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

    public function testPatchPeriodMovePeriodStart() {
        // given
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        $this->assertEquals(3, $period->getPeriodLength());

        /** @var Day $day1 */
        $day1 = static::$fixtures['day1period1'];

        /** @var Day $day2 */
        $day2 = static::$fixtures['day2period1'];

        /** @var Day $day3 */
        $day3 = static::$fixtures['day3period1'];

        $day1_resp = static::$fixtures['dayResponsible1'];
        $day2_resp = static::$fixtures['dayResponsible1day2period1'];

        // Same CampCollaboration Responsible for Day1 and Day2
        $this->assertEquals($day1, $day1_resp->day);
        $this->assertEquals($day2, $day2_resp->day);
        $this->assertEquals($day1_resp->campCollaboration, $day2_resp->campCollaboration);

        // when
        // add new day bevor Day1
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/periods/'.$period->getId(), ['json' => [
                'start' => '2023-04-30',
                'end' => '2023-05-03',
                'moveScheduleEntries' => false,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);

        // then
        /** @var Period $period */
        $period = $this->getEntityManager()->getRepository(Period::class)->find($period->getId());
        // Period has now 4 days
        $this->assertCount(4, $period->days);

        $day1 = $this->getEntityManager()->getRepository(Day::class)->find($day1->getId());
        $day2 = $this->getEntityManager()->getRepository(Day::class)->find($day2->getId());
        $day3 = $this->getEntityManager()->getRepository(Day::class)->find($day3->getId());

        $day1_resp = $this->getEntityManager()->getRepository(DayResponsible::class)->find($day1_resp->getId());
        $day2_resp = $this->getEntityManager()->getRepository(DayResponsible::class)->find($day2_resp->getId());

        // CampCollaboration is now responsible for Day2 (offset=1) and Day3 (offset=2)
        $this->assertEquals(1, $day1_resp->day->dayOffset);
        $this->assertEquals(2, $day2_resp->day->dayOffset);
    }

    public function testPatchPeriodMovesScheduleEntries() {
        /** @var Period $period */
        $period = static::$fixtures['period1camp2'];

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

    public function testPatchPeriodDoNotMoveDayResponsibles() {
        $client = static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()]);
        $client->disableReboot();  // Disable resetting the database between the two requests

        /** @var Period $period */
        $period = static::$fixtures['period2'];

        $client->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'start' => '2023-04-17',
            'end' => '2023-04-18',
            'moveScheduleEntries' => false,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);

        /** @var Period $period */
        $period = $this->getEntityManager()->getRepository(Period::class)->find($period->getId());
        $this->assertCount(2, $period->days);

        // verify dayResponsibles of old day1 (2023-04-15) were removed silently
        $client->request('GET', $this->getIriFor(static::$fixtures['dayResponsible1day1period2']));
        $this->assertResponseStatusCodeSame(404);

        // verify dayResponsibles of old day2 (2023-04-16) were removed silently
        $client->request('GET', $this->getIriFor(static::$fixtures['dayResponsible1day2period2']));
        $this->assertResponseStatusCodeSame(404);

        // verify dayResponsibles of old day3 (2023-04-17) were moved to new day1 (2023-03-27)
        $response = $client->request('GET', '/days?period='.$period->getId())->toArray();
        $days = $response['_embedded']['items'];
        usort($days, fn ($a, $b) => $a['dayOffset'] <=> $b['dayOffset']);

        $day1responsibleCampCollaborations = array_map(fn ($dayResponsible) => $dayResponsible['_links']['campCollaboration']['href'], $days[0]['_embedded']['dayResponsibles']);
        $day2responsibleCampCollaborations = array_map(fn ($dayResponsible) => $dayResponsible['_links']['campCollaboration']['href'], $days[1]['_embedded']['dayResponsibles']);

        $this->assertEqualsCanonicalizing([
            $this->getIriFor(static::$fixtures['campCollaboration1manager']),
            $this->getIriFor(static::$fixtures['campCollaboration2member']),
        ], $day1responsibleCampCollaborations);
        $this->assertEqualsCanonicalizing([], $day2responsibleCampCollaborations);
    }
}
