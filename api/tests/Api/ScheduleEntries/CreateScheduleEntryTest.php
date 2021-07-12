<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateScheduleEntryTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateScheduleEntryIsAllowedForCollaborator() {
        static::createClientWithCredentials()->request('POST', '/schedule_entries', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
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
            $attributes,
            ['period', 'activity'],
            $except
        );
    }
}
