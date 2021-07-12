<?php

namespace App\Tests\Api\Periods;

use App\Entity\Period;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreatePeriodTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests
    // TODO validation for no overlapping periods

    public function testCreatePeriodIsAllowedForCollaborator() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreatePeriodValidatesMissingCamp() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([], ['camp'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreatePeriodValidatesCampThatUserDoesNotHaveAccessTo() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campUnrelated'),
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Item not found for "'.$this->getIriFor('campUnrelated').'".',
        ]);
    }

    public function testCreatePeriodValidatesMissingDescription() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([], ['description'])]);

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

    public function testCreatePeriodValidatesEmptyDescription() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'description' => '',
        ])]);

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

    public function testCreatePeriodValidatesMissingStart() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([], ['start'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'start',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreatePeriodValidatesInvalidStart() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'start' => '20201-01',
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'DateTime::__construct(): Failed to parse time string (20201-01) at position 4 (1): Unexpected character',
        ]);
    }

    public function testCreatePeriodValidatesMissingEnd() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([], ['end'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'end',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreatePeriodValidatesInvalidEnd() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'end' => '20201-01',
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'DateTime::__construct(): Failed to parse time string (20201-01) at position 4 (1): Unexpected character',
        ]);
    }

    public function testCreatePeriodValidatesStartAfterEnd() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'start' => '2021-01-10',
            'end' => '2021-01-09',
        ])]);

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

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Period::class,
            array_merge(['camp' => $this->getIriFor('camp1')], $attributes),
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Period::class,
            $attributes,
            array_merge(['camp'], $except)
        );
    }
}
