<?php

namespace App\Tests\Api\Periods;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdatePeriodTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests
    // TODO moving a period vs changing the time window

    public function testPatchPeriodIsDeniedForAnonymousUser() {
        $period = static::$fixtures['period1'];
        static::createClient()->request('PATCH', '/periods/'.$period->getId(), ['json' => [
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
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
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

    public function testPatchPeriodIsDeniedForInactiveUser() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
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
