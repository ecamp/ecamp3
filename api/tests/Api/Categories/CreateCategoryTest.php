<?php

namespace App\Tests\Api\Categories;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\Category;
use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCategoryTest extends ECampApiTestCase {
    public function testCreateCategoryIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/categories', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateCategoryIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/categories', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateCategoryIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/categories', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateCategoryIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/categories', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateCategoryIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/categories', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateCategoryIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateCategoryInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateCategoryCreatesNewColumnLayoutAsRootContentNode() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $newestColumnLayout = $this->getEntityManager()->getRepository(ContentNode::class)
            ->findBy(['contentType' => static::$fixtures['contentTypeColumnLayout']], ['createTime' => 'DESC'])[0];
        $this->assertJsonContains(['_links' => [
            'rootContentNode' => ['href' => '/content_node/column_layouts/'.$newestColumnLayout->getId()],
        ]]);
    }

    public function testCreateCampDoesntExposeCampPrototypeId() {
        $response = static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['preferredContentTypes'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertArrayNotHasKey('campPrototypeId', $response->toArray());
    }

    public function testCreateCategoryValidatesMissingCamp() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['camp'])]);

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

    public function testCreateCategoryAllowsEmptyPreferredContentTypes() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['preferredContentTypes'])]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateCategoryValidatesMissingShort() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['short'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'short',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryValidatesBlankShort() {
        static::createClientWithCredentials()->request(
            'POST',
            '/categories',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'short' => '',
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'short',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryValidatesTooLongShort() {
        static::createClientWithCredentials()->request(
            'POST',
            '/categories',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'short' => str_repeat('l', 17),
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'short',
                    'message' => 'This value is too long. It should have 16 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryTrimsShort() {
        static::createClientWithCredentials()->request(
            'POST',
            '/categories',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'short' => "  \t LS\t ",
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'short' => 'LS',
            ]
        ));
    }

    public function testCreateCategoryCleansHtmlForShort() {
        static::createClientWithCredentials()->request(
            'POST',
            '/categories',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'short' => 'L<script>alert(1)</script>S',
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'short' => 'LS',
            ]
        ));
    }

    public function testCreateCategoryValidatesMissingName() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['name'])]);

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

    public function testCreateCategoryValidatesBlankName() {
        static::createClientWithCredentials()->request(
            'POST',
            '/categories',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'name' => '',
                    ]
                ),
            ]
        );

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

    public function testCreateCategoryValidatesTooLongName() {
        static::createClientWithCredentials()->request(
            'POST',
            '/categories',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'name' => str_repeat('l', 33),
                    ]
                ),
            ]
        );

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

    public function testCreateCategoryTrimsName() {
        static::createClientWithCredentials()->request(
            'POST',
            '/categories',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'name' => "  \t Lagersport\t ",
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'name' => 'Lagersport',
            ]
        ));
    }

    public function testCreateCategoryCleansHtmlForName() {
        static::createClientWithCredentials()->request(
            'POST',
            '/categories',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'name' => 'Lagerspo<script>alert(1)</script>rt',
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'name' => 'Lagersport',
            ]
        ));
    }

    public function testCreateCategoryValidatesMissingColor() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['color'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'color',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryValidatesInvalidColor() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([
            'color' => 'red',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'color',
                    'message' => 'This value is not valid.',
                ],
            ],
        ]);
    }

    public function testCreateCategoryUsesDefaultForMissingNumberingStyle() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([], ['numberingStyle'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['numberingStyle' => '1']);
    }

    public function testCreateCategoryValidatesInvalidNumberingStyle() {
        static::createClientWithCredentials()->request('POST', '/categories', ['json' => $this->getExampleWritePayload([
            'numberingStyle' => 'x',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'numberingStyle',
                    'message' => 'The value you selected is not a valid choice.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Category::class,
            OperationType::COLLECTION,
            'post',
            array_merge([
                'camp' => $this->getIriFor('camp1'),
                'preferredContentTypes' => [$this->getIriFor('contentTypeSafetyConcept')],
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Category::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['camp', 'preferredContentTypes'],
            $except
        );
    }
}
