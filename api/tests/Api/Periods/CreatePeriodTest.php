<?php

namespace App\Tests\Api\Periods;

use App\Entity\Period;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreatePeriodTest extends ECampApiTestCase {
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
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/periods', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreatePeriodIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/periods', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreatePeriodIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/periods', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreatePeriodIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
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

    public function testCreatePeriodValidatesTooLongDescription() {
        static::createClientWithCredentials()->request(
            'POST',
            '/periods',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'description' => str_repeat('l', 33),
                    ]
                ),
            ]
        );

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

    public function testCreatePeriodTrimsDescription() {
        static::createClientWithCredentials()->request(
            'POST',
            '/periods',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'description' => " \t Vorlager \t ",
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'description' => 'Vorlager',
            ]
        ));
    }

    public function testCreatePeriodCleansForbiddenCharactersForDescription() {
        static::createClientWithCredentials()->request(
            'POST',
            '/periods',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'description' => "Vorl\n\tager",
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'description' => 'Vorlager',
            ]
        ));
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

    public function testCreatePeriodValidatesInvalidStartDateFormat() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'start' => '20201-01',
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => "Parsing datetime string \"20201-01\" using format \"!Y-m-d\" resulted in 1 errors: \nat position 4: The separation symbol could not be found",
        ]);
    }

    public function testCreatePeriodValidatesInvalidStartDateTime() {
        static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'start' => '2021-01-01T05:31+01:00',
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => "Parsing datetime string \"2021-01-01T05:31+01:00\" using format \"!Y-m-d\" resulted in 1 errors: \nat position 10: Trailing data",
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
            'detail' => "Parsing datetime string \"20201-01\" using format \"!Y-m-d\" resulted in 1 errors: \nat position 4: The separation symbol could not be found",
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

    public function testCreatePeriodCreatesDays() {
        $response = static::createClientWithCredentials()->request('POST', '/periods', ['json' => $this->getExampleWritePayload([
            'start' => '2021-03-01',
            'end' => '2021-03-04',
        ])]);

        $this->assertResponseStatusCodeSame(201);

        /** @var Period $period */
        $period = $this->getEntityManager()->getRepository(Period::class)->find($response->toArray()['id']);

        $this->assertCount(4, $period->days);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Period::class,
            '/periods',
            'post',
            array_merge(['camp' => $this->getIriFor('camp1')], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Period::class,
            '/periods',
            'get',
            $attributes,
            ['camp'],
            $except
        );
    }
}
