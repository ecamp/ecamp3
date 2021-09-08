<?php

namespace App\Tests\Api\Periods;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\Period;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreatePeriodTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests
    // TODO validation for no overlapping periods

    public function testCreatePeriodIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/periods', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreatePeriodIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('POST', '/periods', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreatePeriodIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('POST', '/periods', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreatePeriodIsDeniedForGuest() {
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('POST', '/periods', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreatePeriodIsAllowedForMember() {
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('POST', '/periods', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreatePeriodIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreatePeriodInCampPrototypeIsDeniedForUnrelateduser() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
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
            OperationType::COLLECTION,
            'post',
            array_merge(['camp' => $this->getIriFor('camp1')], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Period::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['camp'],
            $except
        );
    }
}
