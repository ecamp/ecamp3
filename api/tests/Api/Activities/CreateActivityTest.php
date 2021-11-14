<?php

namespace App\Tests\Api\Activities;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\Activity;
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

    public function testCreateActivityIsNotPossibleForUnrelatedUserBecauseCategoryIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('category1').'".',
        ]);
    }

    public function testCreateActivityIsNotPossibleForInactiveCollaboratorBecauseCategoryIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('category1').'".',
        ]);
    }

    public function testCreateActivityIsDeniedForGuest() {
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('POST', '/activities', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateActivityIsAllowedForMember() {
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
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

    public function testCreateActivityValidatesMissingCategory() {
        static::createClientWithCredentials()->request('POST', '/activities', ['json' => $this->getExampleWritePayload([], ['category'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'category',
                    'message' => 'This value should not be null.',
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
                            'width' => 6,
                        ],
                        [
                            'slot' => '2',
                            'width' => 6,
                        ],
                    ],
                    'slot' => '',
                    'position' => 0,
                    'instanceName' => 'columnLayout1',
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
                    'instanceName' => 'columnLayoutChild1',
                    'contentTypeName' => 'ColumnLayout',
                ],
            ],
        ]]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Activity::class,
            OperationType::COLLECTION,
            'post',
            array_merge(['category' => $this->getIriFor('category1')], $attributes),
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
