<?php

namespace App\Tests\Api\Periods;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdatePeriodTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests
    // TODO moving a period vs changing the time window

    public function testPatchPeriodIsAllowedForCollaborator() {
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

    public function testPatchPeriodDisallowsChangingCamp() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'camp' => $this->getIriFor('camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" are unknown).',
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

    public function testPatchPeriodValidatesInvalidStart() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'start' => 'something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'DateTime::__construct(): Failed to parse time string (something) at position 0 (s): The timezone could not be found in the database',
        ]);
    }

    public function testPatchPeriodValidatesInvalidEnd() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
            'end' => 'something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'DateTime::__construct(): Failed to parse time string (something) at position 0 (s): The timezone could not be found in the database',
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
}
