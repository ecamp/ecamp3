<?php

namespace App\Tests\Api\ScheduleEntries;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateScheduleEntryTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testCreateScheduleEntryIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateScheduleEntryIsNotPossibleForUnrelatedUserBecausePeriodIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testCreateScheduleEntryIsNotPossibleForInactiveCollaboratorBecausePeriodIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testCreateScheduleEntryIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateScheduleEntryIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateScheduleEntryIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateScheduleEntryInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([
            'period' => $this->getIriFor('period1campPrototype'),
            'activity' => $this->getIriFor('activity1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateScheduleEntryValidatesMissingPeriod() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([], ['period'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateScheduleEntryValidatesMissingActivity() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([], ['activity'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [[],
                [
                    'propertyPath' => 'activity',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateScheduleEntryValidatesPeriodFromDifferentCampThanActivity() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([
            'period' => $this->getIriFor('period1camp2'),
            'activity' => $this->getIriFor('activity1'),
        ])]);

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

    public function testCreateScheduleEntryUsesDefaultForMissingStart() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([], ['start'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'start' => '2023-05-01T00:00:00+00:00',
        ]);
    }

    public function testCreateScheduleEntryValidatesStartBeforePeriodStart() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([
            'start' => '2023-04-30T23:59:59+00:00',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'start',
                    'message' => 'This value should be greater than or equal to May 1, 2023, 12:00 AM.',
                ],
            ],
        ]);
    }

    public function testCreateScheduleEntryUsesDefaultForMissingEnd() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([], ['end'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'end' => '2023-05-01T01:00:00+00:00',
        ]);
    }

    public function testCreateScheduleEntryValidatesNegativeLength() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([
            'start' => '2023-05-01T01:30:00+00:00',
            'end' => '2023-05-01T01:00:00+00:00',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'end',
                    'message' => 'This value should be greater than May 1, 2023, 1:30 AM.',
                ],
            ],
        ]);
    }

    public function testCreateScheduleEntryUsesDefaultForMissingLeft() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([], ['left'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'left' => 0,
        ]);
    }

    public function testCreateScheduleEntryUsesDefaultForMissingWidth() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([], ['width'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'width' => 1,
        ]);
    }

    public function testCreateScheduleEntryWithReversedPropertyOrder() {
        $result = static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => [
            'start' => '2023-05-01T04:00:00+00:00',
            'end' => '2023-05-01T05:00:00+00:00',
            'period' => $this->getIriFor('period1'),
            'activity' => $this->getIriFor('activity1'),
        ]]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertJsonContains([
            'start' => '2023-05-01T04:00:00+00:00',
            'end' => '2023-05-01T05:00:00+00:00',
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ScheduleEntry::class,
            Post::class,
            array_merge([
                'start' => '2023-05-01T00:30:00+00:00',
                'end' => '2023-05-01T01:30:00+00:00',
                'period' => $this->getIriFor('period1'),
                'activity' => $this->getIriFor('activity1'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ScheduleEntry::class,
            Get::class,
            array_merge([
                'start' => '2023-05-01T00:30:00+00:00',
                'end' => '2023-05-01T01:30:00+00:00',
            ], $attributes),
            ['period', 'activity'],
            $except
        );
    }
}
