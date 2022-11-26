<?php

namespace App\Tests\Api\Camps;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\Camp;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCampTest extends ECampApiTestCase {
    public function testCreateCampWhenNotLoggedIn() {
        static::createBasicClient()->request('POST', '/camps', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateCampWhenLoggedIn() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'isPrototype' => false,
        ]));
    }

    public function testCreateCampDoesntExposeCampPrototypeId() {
        $response = static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertArrayNotHasKey('campPrototypeId', $response->toArray());
    }

    public function testCreateCampSetsCreatorToAuthenticatedUser() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'creator' => ['href' => '/users/'.static::$fixtures['user1manager']->getId()],
        ]]);
    }

    public function testCreateCampSetsOwnerToAuthenticatedUser() {
        $response = static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload()]);

        /** @var UserRepository $userRepository */
        $userRepository = $this->getEntityManager()->getRepository(User::class);
        $user = $userRepository->loadUserByIdentifier('test@example.com');

        $this->assertResponseStatusCodeSame(201);
        $camp = $this->getEntityManager()->getRepository(Camp::class)->find($response->toArray()['id']);
        $this->assertEquals($user->getId(), $camp->owner->getId());
    }

    public function testCreateCampValidatesMissingPeriods() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([], ['periods'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'periods',
                    'message' => 'This collection should contain 1 element or more.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesEmptyPeriods() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'periods' => [],
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'periods',
                    'message' => 'This collection should contain 1 element or more.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesInvalidPeriods() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'periods' => [
                [
                    'description' => 'Hauptlager',
                    'start' => '2022-01-10', // start date after end date is invalid
                    'end' => '2022-01-08',
                ],
            ],
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'periods[0].start',
                    'message' => 'This value should be less than or equal to Jan 8, 2022, 12:00 AM.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesOverlappingPeriods() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'periods' => [
                [
                    'description' => 'Aufbau',
                    'start' => '2022-01-07',
                    'end' => '2022-01-09',
                ],
                [
                    'description' => 'Hauptlager',
                    'start' => '2022-01-08',
                    'end' => '2022-01-10',
                ],
                [
                    'description' => 'Nachweekend',
                    'start' => '2022-01-10',
                    'end' => '2022-01-11',
                ],
            ],
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'periods[0].end',
                    'message' => 'Periods must not overlap.',
                ],
                [
                    'propertyPath' => 'periods[1].start',
                    'message' => 'Periods must not overlap.',
                ],
                [
                    'propertyPath' => 'periods[1].end',
                    'message' => 'Periods must not overlap.',
                ],
                [
                    'propertyPath' => 'periods[2].start',
                    'message' => 'Periods must not overlap.',
                ],
            ],
        ]);
    }

    public function testCreateCampCreatesPeriodAndDays() {
        $response = static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'periods' => [
                [
                    'description' => 'Woche1',
                    'start' => '2023-01-01',
                    'end' => '2023-01-08',
                ],
                [
                    'description' => 'Woche2',
                    'start' => '2023-02-01',
                    'end' => '2023-02-04',
                ],
            ],
        ])]);

        $this->assertResponseStatusCodeSame(201);

        /** @var Camp $camp */
        $camp = $this->getEntityManager()->getRepository(Camp::class)->find($response->toArray()['id']);
        $this->assertCount(2, $camp->periods);

        $woche1 = $camp->periods[0];
        $this->assertCount(8, $woche1->days);

        $woche2 = $camp->periods[1];
        $this->assertCount(4, $woche2->days);
    }

    public function testCreateCampTrimsName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'name' => " So-La\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'name' => 'So-La',
        ]));
    }

    public function testCreateCampCleansForbiddenCharactersFromName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'name' => "So-\n\tLa",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'name' => 'So-La',
        ]));
    }

    public function testCreateCampValidatesMissingName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([], ['name'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesBlankName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'name' => '',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesLongName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'name' => 'A very long camp name which is not really useful',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCampTrimsTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'title' => " Sommerlager\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'title' => 'Sommerlager',
        ]));
    }

    public function testCreateCampCleansForbiddenCharactersFromTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'title' => "Sommer\n\tlager",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'title' => 'Sommerlager',
        ]));
    }

    public function testCreateCampValidatesMissingTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([], ['title'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesBlankTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'title' => '',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesLongTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'title' => 'A very long camp title which is not really useful',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCampTrimsMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'motto' => " Dschungelbuch\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'motto' => 'Dschungelbuch',
        ]));
    }

    public function testCreateCampCleansForbiddenCharactersFromMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'motto' => "this\n\t\u{202E} is 'a' <sample> text😀 \\",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'motto' => "this is 'a' <sample> text😀 \\",
        ]));
    }

    public function testCreateCampAllowsMissingMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([], ['motto'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => null,
        ]);
    }

    public function testCreateCampAllowsNullMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'motto' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'motto' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => '',
        ]);
    }

    public function testCreateCampValidatesLongMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'motto' => 'This camp has an extremely long motto. This camp has an extremely long motto. This camp has an extremely long motto. This camp ha',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'motto',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCampTrimsAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressName' => " Auf dem Hügel\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'addressName' => 'Auf dem Hügel',
        ]));
    }

    public function testCreateCampCleansForbiddenCharactersFromAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressName' => "Auf dem Hügel\n\t",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'addressName' => 'Auf dem Hügel',
        ]));
    }

    public function testCreateCampAllowsMissingAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([], ['addressName'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => null,
        ]);
    }

    public function testCreateCampAllowsNullAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressName' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressName' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => '',
        ]);
    }

    public function testCreateCampValidatesLongAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressName' => 'This camp has an extremely long address. This camp has an extremely long address. This camp has an extremely long address. This!!',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'addressName',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCampTrimsAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressStreet' => " Suppenstrasse 123a\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'addressStreet' => 'Suppenstrasse 123a',
        ]));
    }

    public function testCreateCampCleansForbiddenCharactersFromAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressStreet' => "Suppenstrasse \n\t123a",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'addressStreet' => 'Suppenstrasse 123a',
        ]));
    }

    public function testCreateCampAllowsMissingAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([], ['addressStreet'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => null,
        ]);
    }

    public function testCreateCampAllowsNullAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressStreet' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressStreet' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => '',
        ]);
    }

    public function testCreateCampValidatesLongAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressStreet' => 'This camp has an extremely long address. This camp has an extremely long address. This camp has an extremely long address. This!!',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'addressStreet',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCampTrimsAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressZipcode' => " 8000\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'addressZipcode' => '8000',
        ]));
    }

    public function testCreateCampCleansForbiddenCharactersFromAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressZipcode' => "800\n\t0",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'addressZipcode' => '8000',
        ]));
    }

    public function testCreateCampAllowsMissingAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([], ['addressZipcode'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => null,
        ]);
    }

    public function testCreateCampAllowsNullAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressZipcode' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressZipcode' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => '',
        ]);
    }

    public function testCreateCampValidatesLongAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressZipcode' => 'This camp has an extremely long zipcode. This camp has an extremely long zipcode. This camp has an extremely long zipcode. This!!',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'addressZipcode',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCampTrimsAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressZipcode' => " Unterberg\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'addressZipcode' => 'Unterberg',
        ]));
    }

    public function testCreateCampCleansForbiddenCharactersFromAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressCity' => "Unter\n\tberg",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'addressCity' => 'Unterberg',
        ]));
    }

    public function testCreateCampAllowsMissingAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([], ['addressCity'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => null,
        ]);
    }

    public function testCreateCampAllowsNullAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressCity' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressCity' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => '',
        ]);
    }

    public function testCreateCampValidatesLongAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'addressCity' => 'This camp has an extremely long city. This camp has an extremely long city. This camp has an extremely long city. This camp, I\'m telling you!',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'addressCity',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCampFromPrototype() {
        /** @var Camp $campPrototype */
        $campPrototype = self::$fixtures['campPrototype'];

        $response = static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'campPrototype' => $this->getIriFor($campPrototype),
        ])]);

        $this->assertResponseStatusCodeSame(201);

        $camp = $this->getEntityManager()->getRepository(Camp::class)->find($response->toArray()['id']);
        $this->assertEquals($campPrototype->getId(), $camp->campPrototypeId);
        $this->assertCount(1, $camp->categories);
        $this->assertCount(2, $camp->materialLists);
    }

    public function testCreateCampReturnsProperDatesInTimezoneAheadOfUTC() {
        date_default_timezone_set('Asia/Singapore');
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'periods' => [
                [
                    'description' => 'Hauptlager',
                    'start' => '2022-01-08',
                    'end' => '2022-01-10',
                ],
            ],
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '_embedded' => [
                'periods' => [
                    [
                        'start' => '2022-01-08',
                        'end' => '2022-01-10',
                    ],
                ],
            ],
        ]);
    }

    public function testCreateCampReturnsProperDatesInTimezoneBehindUTC() {
        date_default_timezone_set('America/New_York');
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'periods' => [
                [
                    'description' => 'Hauptlager',
                    'start' => '2022-01-08',
                    'end' => '2022-01-10',
                ],
            ],
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '_embedded' => [
                'periods' => [
                    [
                        'start' => '2022-01-08',
                        'end' => '2022-01-10',
                    ],
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(Camp::class, OperationType::COLLECTION, 'post', $attributes, [], $except);
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Camp::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['periods'],
            $except
        );
    }
}
