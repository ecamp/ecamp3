<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateScheduleEntryTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testPatchScheduleEntryIsDeniedForAnonymousUser() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createBasicClient()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'period' => $this->getIriFor('period2'),
            'start' => '2023-04-15T00:10:00+00:00',
            'end' => '2023-04-15T00:40:00+00:00',
            'left' => 0.3,
            'width' => 0.7,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchScheduleEntryIsDeniedForUnrelatedUser() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
                'period' => $this->getIriFor('period2'),
                'start' => '2023-04-15T00:10:00+00:00',
                'end' => '2023-04-15T00:40:00+00:00',
                'left' => 0.3,
                'width' => 0.7,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchScheduleEntryIsDeniedForInactiveCollaborator() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
                'period' => $this->getIriFor('period2'),
                'start' => '2023-04-15T00:10:00+00:00',
                'end' => '2023-04-15T00:40:00+00:00',
                'left' => 0.3,
                'width' => 0.7,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchScheduleEntryIsDeniedForGuest() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
                'period' => $this->getIriFor('period2'),
                'start' => '2023-04-15T00:10:00+00:00',
                'end' => '2023-04-15T00:40:00+00:00',
                'left' => 0.3,
                'width' => 0.7,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchScheduleEntryIsAllowedForMember() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
                'period' => $this->getIriFor('period2'),
                'start' => '2023-04-15T00:10:00+00:00',
                'end' => '2023-04-15T00:40:00+00:00',
                'left' => 0.3,
                'width' => 0.7,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'start' => '2023-04-15T00:10:00+00:00',
            'end' => '2023-04-15T00:40:00+00:00',
            'left' => 0.3,
            'width' => 0.7,
            '_links' => [
                'period' => ['href' => $this->getIriFor('period2')],
            ],
        ]);
    }

    public function testPatchScheduleEntryIsAllowedForManager() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'period' => $this->getIriFor('period2'),
            'start' => '2023-04-15T00:10:00+00:00',
            'end' => '2023-04-15T00:40:00+00:00',
            'left' => 0.3,
            'width' => 0.7,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'start' => '2023-04-15T00:10:00+00:00',
            'end' => '2023-04-15T00:40:00+00:00',
            'left' => 0.3,
            'width' => 0.7,
            '_links' => [
                'period' => ['href' => $this->getIriFor('period2')],
            ],
        ]);
    }

    public function testPatchScheduleEntryInCampPrototypeIsDeniedForUnrelatedUser() {
        $scheduleEntry = static::$fixtures['scheduleEntry1period1campPrototype'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'period' => $this->getIriFor('period2'),
            'start' => '2023-04-15T00:10:00+00:00',
            'end' => '2023-04-15T00:40:00+00:00',
            'left' => 0.3,
            'width' => 0.7,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchScheduleEntryDisallowsChangingActivity() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'activity' => $this->getIriFor('activity2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("activity" is unknown).',
        ]);
    }

    public function testPatchScheduleEntryValidatesPeriodFromSameCamp() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'period' => $this->getIriFor('period1camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testPatchScheduleEntryValidatesMissingStart() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'start' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The data is either not an string, an empty string, or null; you should pass a string that can be parsed with the passed format or a valid DateTime string.',
        ]);
    }

    public function testPatchScheduleEntryValidatesStartBeforePeriodStart() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        $response = static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'start' => '2023-04-30T23:59:59+00:00',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'start',
                    'message' => 'This value should be greater than or equal to May 1, 2023, 12:00 AM.',
                ],
            ],
        ]);
    }

    public function testPatchScheduleEntryValidatesMissingEnd() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'end' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The data is either not an string, an empty string, or null; you should pass a string that can be parsed with the passed format or a valid DateTime string.',
        ]);
    }

    public function testPatchScheduleEntryValidatesNegativeLength() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'start' => '2023-05-01T00:40:00+00:00',
            'end' => '2023-05-01T00:10:00+00:00',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'end',
                    'message' => 'This value should be greater than May 1, 2023, 12:40 AM.',
                ],
            ],
        ]);
    }

    public function testPatchScheduleEntryAllowsMissingLeft() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'left' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'left' => null,
        ]);
    }

    public function testPatchScheduleEntryAllowsMissingWidth() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'width' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'width' => null,
        ]);
    }

    public function testPatchScheduleEntryValidatesEndAfterPeriodEnd() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'start' => '2023-05-03T23:00:00+00:00',
            'end' => '2023-05-04T00:01:00+00:00',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'end',
                    'message' => 'This value should be less than or equal to May 4, 2023, 12:00 AM.',
                ],
            ],
        ]);
    }

    public function testPatchScheduleEntryAllowsToEndAtMidnightOfLastDay() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'start' => '2023-05-03T23:00:00+00:00',
            'end' => '2023-05-04T00:00:00+00:00',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testPatchScheduleEntryWorksCorrectlyDuringClockChanges() {
        date_default_timezone_set('Europe/Zurich'); // clock changes to daylight saving on 2023-03-26 at 2am

        $scheduleEntry = static::$fixtures['scheduleEntry1period1camp2'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'start' => '2023-03-25T22:00:00+00:00',
            'end' => '2023-03-26T09:00:00+00:00',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'start' => '2023-03-25T22:00:00+00:00',
            'end' => '2023-03-26T09:00:00+00:00',
        ]);

        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = $this->getEntityManager()->getRepository(ScheduleEntry::class)->find($scheduleEntry->getId());

        $this->assertEquals(22 * 60, $scheduleEntry->startOffset);
        $this->assertEquals((24 + 9) * 60, $scheduleEntry->endOffset);
    }
}
