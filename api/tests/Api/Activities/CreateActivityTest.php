<?php

namespace App\Tests\Api\Activities;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\Activity;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;
use App\Tests\Constraints\CompatibleHalResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function PHPUnit\Framework\assertThat;

/**
 * @internal
 */
class CreateActivityTest extends ECampApiTestCase {
    public function testCreateActivityIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/activities', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateActivityIsNotPossibleForUnrelatedUserBecausePeriodIsNotReadable() {
        /** @var User $user */
        $user = static::getFixture('user4unrelated');
        static::createClientWithCredentials(['email' => $user->getEmail()])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testCreateActivityIsNotPossibleForInactiveCollaboratorBecausePeriodIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testCreateActivityIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateActivityIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateActivityIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/activities', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateActivityInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/activities', ['json' => $this->getExampleWritePayload([
            'category' => $this->getIriFor('category1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateActivitySetsCampToCategorysCamp() {
        static::createClientWithCredentials()->request('POST', '/activities', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'camp' => ['href' => '/camps/'.static::$fixtures['camp1']->getId()],
        ]]);
    }

    public function testCreateActivityValidatesMissingCategoryBecauseScheduleEntryPeriodAndCategoryMustBelongToSameCamp() {
        static::createClientWithCredentials()->request(
            'POST',
            '/activities',
            ['json' => $this->getExampleWritePayload([], ['category'])]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'scheduleEntries[0].period',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testCreateActivityValidatesMissingTitle() {
        static::createClientWithCredentials()->request('POST', '/activities', ['json' => $this->getExampleWritePayload([], ['title'])]);

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

    public function testCreateActivityValidatesNullTitle() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'title' => null,
                        ]
                    ),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'The type of the "title" attribute must be "string", "NULL" given.',
        ]);
    }

    public function testCreateActivityValidatesTitleMinLength() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'title' => '',
                        ]
                    ),
                ]
            )
        ;

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

    public function testCreateActivityValidatesTitleMaxLength() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'title' => str_repeat('a', 33),
                        ]
                    ),
                ]
            )
        ;

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

    public function testCreateActivityCleansForbiddenCharactersFromTitle() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'title' => "this\n\t\u{202E} is 'a' <sample> text😀 \\",
                        ]
                    ),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'title' => "this is 'a' <sample> text😀 \\",
        ]));
    }

    public function testCreateActivityTrimsTitle() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'title' => str_repeat('a', 32)." \t",
                        ]
                    ),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'title' => str_repeat('a', 32),
        ]));
    }

    public function testCreateActivityAllowsMissingLocation() {
        static::createClientWithCredentials()->request('POST', '/activities', ['json' => $this->getExampleWritePayload([], ['location'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['location' => '']);
    }

    public function testCreateActivityValidatesNullLocation() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'location' => null,
                        ]
                    ),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'The type of the "location" attribute must be "string", "NULL" given.',
        ]);
    }

    public function testCreateActivityValidatesLocationMaxLength() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'location' => str_repeat('a', 65),
                        ]
                    ),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'location',
                    'message' => 'This value is too long. It should have 64 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateActivityCleansForbiddenCharactersFromLocation() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'location' => "this\n\t\u{202E} is 'a' <sample> text😀 \\",
                        ]
                    ),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'location' => "this is 'a' <sample> text😀 \\",
        ]));
    }

    public function testCreateActivityTrimsLocation() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/activities',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'location' => str_repeat('a', 64)." \t",
                        ]
                    ),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'location' => str_repeat('a', 64),
        ]));
    }

    public function testCreateActivityCopiesContentFromCategory() {
        $response = static::createClientWithCredentials()->request('POST', '/activities', ['json' => $this->getExampleWritePayload()]);

        $id = $response->toArray()['id'];
        $newActivity = $this->getEntityManager()->getRepository(Activity::class)->find($id);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_embedded' => [
            'contentNodes' => [
                // copy of columnLayout1
                [
                    '_links' => [
                        'contentType' => [
                            'href' => $this->getIriFor('contentTypeColumnLayout'),
                        ],
                    ],
                    'data' => [
                        'columns' => [
                            [
                                'slot' => '1',
                                'width' => 12,
                            ],
                        ],
                    ],
                    'slot' => null,
                    'position' => 0,
                    'instanceName' => 'columnLayout2',
                    'contentTypeName' => 'ColumnLayout',
                ],

                // copy of columnLayoutChild1
                [
                    '_links' => [
                        'contentType' => [
                            'href' => $this->getIriFor('contentTypeColumnLayout'),
                        ],
                    ],
                    'data' => [
                        'columns' => [
                            [
                                'slot' => '1',
                                'width' => 12,
                            ],
                        ],
                    ],
                    'slot' => '2',
                    'position' => 0,
                    'instanceName' => 'columnLayout2Child',
                    'contentTypeName' => 'ColumnLayout',
                ],
            ],
        ]]);
    }

    public function testCreateActivityAllowsEmbeddingScheduleEntries() {
        static::createClientWithCredentials()->request(
            'POST',
            '/activities',
            ['json' => $this->getExampleWritePayload(
                [
                    'scheduleEntries' => [
                        [
                            'period' => $this->getIriFor('period1'),
                            'start' => '2023-05-01T15:00:00+00:00',
                            'end' => '2023-05-01T16:00:00+00:00',
                        ],
                    ],
                ],
                []
            )]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '_embedded' => [
                'scheduleEntries' => [
                    [
                        'start' => '2023-05-01T15:00:00+00:00',
                        'end' => '2023-05-01T16:00:00+00:00',
                    ],
                ],
            ],
        ]);
    }

    public function testCreateActivityValidatesScheduleEntries() {
        static::createClientWithCredentials()->request(
            'POST',
            '/activities',
            ['json' => $this->getExampleWritePayload(
                [
                    'scheduleEntries' => [
                        [
                            'period' => $this->getIriFor('period1camp2'),
                        ],
                    ],
                ],
                []
            )]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'scheduleEntries[0].period',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testCreateActivityValidatesMissingScheduleEntries() {
        static::createClientWithCredentials()->request(
            'POST',
            '/activities',
            ['json' => $this->getExampleWritePayload(
                [
                    'scheduleEntries' => [],
                ],
                []
            )]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateActivityFromCopySourceValidatesAccess() {
        static::createClientWithCredentials(['email' => static::$fixtures['user8memberOnlyInCamp2']->getEmail()])->request(
            'POST',
            '/activities',
            ['json' => $this->getExampleWritePayload(
                [
                    'copyActivitySource' => $this->getIriFor('activity1'),
                    'category' => $this->getIriFor('category1camp2'),
                    'scheduleEntries' => [
                        [
                            'period' => $this->getIriFor('period1camp2'),
                            'start' => '2023-03-25T15:00:00+00:00',
                            'end' => '2023-03-25T16:00:00+00:00',
                        ],
                    ],
                ],
                []
            )]
        );

        // No Access on activity1 -> BadRequest
        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateActivityFromCopySourceWithinSameCamp() {
        static::createClientWithCredentials()->request(
            'POST',
            '/activities',
            ['json' => $this->getExampleWritePayload(
                [
                    'copyActivitySource' => $this->getIriFor('activity1'),
                    'category' => $this->getIriFor('category1'),
                ],
                []
            )]
        );

        // Activity created
        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateActivityFromCopySourceAcrossCamp() {
        static::createClientWithCredentials()->request(
            'POST',
            '/activities',
            ['json' => $this->getExampleWritePayload(
                [
                    'copyActivitySource' => $this->getIriFor('activity1'),
                    'category' => $this->getIriFor('category1camp2'),
                    'scheduleEntries' => [
                        [
                            'period' => $this->getIriFor('period1camp2'),
                            'start' => '2023-03-25T15:00:00+00:00',
                            'end' => '2023-03-25T16:00:00+00:00',
                        ],
                    ],
                ],
                []
            )]
        );

        // Activity created
        $this->assertResponseStatusCodeSame(201);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCreateResponseStructureMatchesReadResponseStructure() {
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $createResponse = $client->request(
            'POST',
            '/activities',
            [
                'json' => $this->getExampleWritePayload(),
            ]
        );

        $this->assertResponseStatusCodeSame(201);

        $createArray = $createResponse->toArray();
        $newItemLink = $createArray['_links']['self']['href'];
        $getItemResponse = $client->request('GET', $newItemLink);

        assertThat($createArray, CompatibleHalResponse::isHalCompatibleWith($getItemResponse->toArray()));
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Activity::class,
            Post::class,
            array_merge([
                'copyActivitySource' => null,
                'category' => $this->getIriFor('category1'),
                'progressLabel' => null,
                'scheduleEntries' => [
                    [
                        'period' => $this->getIriFor('period1'),
                        'start' => '2023-05-01T15:00:00+00:00',
                        'end' => '2023-05-01T16:00:00+00:00',
                    ],
                ],
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Activity::class,
            Get::class,
            $attributes,
            ['category', 'progressLabel'],
            $except
        );
    }
}
