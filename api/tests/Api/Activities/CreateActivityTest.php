<?php

namespace App\Tests\Api\Activities;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\Activity;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateActivityTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

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
        $user = static::$fixtures['user4unrelated'];
        static::createClientWithCredentials(['username' => $user->getUsername()])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testCreateActivityIsNotPossibleForInactiveCollaboratorBecausePeriodIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testCreateActivityIsDeniedForGuest() {
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateActivityIsAllowedForMember() {
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
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
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateActivityAllowsMissingLocation() {
        static::createClientWithCredentials()->request('POST', '/activities', ['json' => $this->getExampleWritePayload([], ['location'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['location' => '']);
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
                        'owner' => [
                            'href' => $this->getIriFor($newActivity),
                        ],
                        'ownerCategory' => [
                            'href' => $this->getIriFor('category1'),
                        ],
                    ],
                    'columns' => [
                        [
                            'slot' => '1',
                            'width' => 12,
                        ],
                    ],
                    'slot' => '',
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
                        'owner' => [
                            'href' => $this->getIriFor($newActivity),
                        ],
                        'ownerCategory' => [
                            'href' => $this->getIriFor('category1'),
                        ],
                    ],
                    'columns' => [
                        [
                            'slot' => '1',
                            'width' => 12,
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

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Activity::class,
            OperationType::COLLECTION,
            'post',
            array_merge([
                'category' => $this->getIriFor('category1'),
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
            OperationType::ITEM,
            'get',
            $attributes,
            ['category'],
            $except
        );
    }
}
