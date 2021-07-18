<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateScheduleEntryTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testPatchScheduleEntryIsAllowedForCollaborator() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'period' => $this->getIriFor('period2'),
            'periodOffset' => 10,
            'length' => 30,
            'left' => 0.3,
            'width' => 0.7,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'periodOffset' => 10,
            'length' => 30,
            'left' => 0.3,
            'width' => 0.7,
            '_links' => [
                'period' => ['href' => $this->getIriFor('period2')],
            ],
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

    public function testPatchScheduleEntryValidatesMissingPeriodOffset() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'periodOffset' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "periodOffset" attribute must be "int", "NULL" given.',
        ]);
    }

    public function testPatchScheduleEntryValidatesNegativePeriodOffset() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'periodOffset' => -180,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

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

    public function testPatchScheduleEntryValidatesMissingLength() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'length' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "length" attribute must be "int", "NULL" given.',
        ]);
    }

    public function testPatchScheduleEntryValidatesNegativeLength() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('PATCH', '/schedule_entries/'.$scheduleEntry->getId(), ['json' => [
            'length' => -10,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

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
}
