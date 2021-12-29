<?php

namespace App\Tests\Api\ScheduleEntries;

use ApiPlatform\Core\Api\OperationType;
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
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testCreateScheduleEntryIsNotPossibleForInactiveCollaboratorBecausePeriodIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testCreateScheduleEntryIsDeniedForGuest() {
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateScheduleEntryIsAllowedForMember() {
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
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

    public function testCreateScheduleEntryUsesDefaultForMissingPeriodOffset() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([], ['periodOffset'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'periodOffset' => 0,
        ]);
    }

    public function testCreateScheduleEntryValidatesNegativePeriodOffset() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([
            'periodOffset' => -60,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'periodOffset',
                    'message' => 'This value should be greater than or equal to 0.',
                ],
            ],
        ]);
    }

    public function testCreateScheduleEntryValidatesMissingLength() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([], ['length'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'length',
                    'message' => 'This value should be greater than 0.',
                ],
            ],
        ]);
    }

    public function testCreateScheduleEntryValidatesNegativeLength() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload([
            'length' => -60,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'length',
                    'message' => 'This value should be greater than 0.',
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

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ScheduleEntry::class,
            OperationType::COLLECTION,
            'post',
            array_merge([
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
            OperationType::ITEM,
            'get',
            $attributes,
            ['period', 'activity'],
            $except
        );
    }
}
