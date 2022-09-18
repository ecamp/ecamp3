<?php

namespace App\Tests\Api\MaterialItems;

use App\Entity\MaterialItem;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateMaterialItemTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testCreateMaterialItemIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateMaterialItemIsNotPossibleForUnrelatedUserBecauseMaterialListIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('POST', '/material_items', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('materialList1').'".',
        ]);
    }

    public function testCreateMaterialItemIsNotPossibleForInactiveCollaboratorBecauseMaterialListIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('POST', '/material_items', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('materialList1').'".',
        ]);
    }

    public function testCreateMaterialItemIsDeniedForGuest() {
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('POST', '/material_items', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateMaterialItemIsAllowedForMember() {
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('POST', '/material_items', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateMaterialItemIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateMaterialItemValidatesMissingMaterialList() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([], ['materialList'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'materialList',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialItemInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'materialList' => $this->getIriFor('materialList1campPrototype'),
            'period' => $this->getIriFor('period1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateMaterialItemWithMaterialNodeInsteadOfPeriodIsPossible() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'materialNode' => $this->getIriFor('materialNode1'),
            'period' => null,
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            '_links' => [
                'materialNode' => ['href' => $this->getIriFor('materialNode1')],
                // 'period' => null,
            ],
        ]));
    }

    public function testCreateMaterialItemWithMaterialNodeFromQueryParameter() {
        static::createClientWithCredentials()->request('POST', '/material_items?materialNode='.urlencode($this->getIriFor('materialNode1')), ['json' => $this->getExampleWritePayload(
            except: [
                'materialNode',
                'period',
            ]
        )]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            '_links' => [
                'materialNode' => ['href' => $this->getIriFor('materialNode1')],
                // 'period' => null,
            ],
        ]));
    }

    public function testCreateMaterialItemWithPeriodFromQueryParameter() {
        static::createClientWithCredentials()->request('POST', '/material_items?period='.urlencode($this->getIriFor('period1')), ['json' => $this->getExampleWritePayload(
            except: [
                'materialNode',
                'period',
            ]
        )]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            '_links' => [
                // 'materialNode' => null ,
                'period' => ['href' => $this->getIriFor('period1')],
            ],
        ]));
    }

    public function testCreateMaterialItemValidatesMissingPeriodAndMaterialNode() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([], ['period', 'materialNode'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Either this value or materialNode should not be null.',
                ],
                [
                    'propertyPath' => 'materialNode',
                    'message' => 'Either this value or period should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialItemValidatesConflictingPeriodAndMaterialNode() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'period' => $this->getIriFor('period1'),
            'materialNode' => $this->getIriFor('materialNode1'),
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Either this value or materialNode should be null.',
                ],
                [
                    'propertyPath' => 'materialNode',
                    'message' => 'Either this value or period should be null.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialItemValidatesPeriodFromDifferentCamp() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'period' => $this->getIriFor('period1camp2'),
            'materialNode' => null,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialItemValidatesMaterialNodeFromDifferentCamp() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'period' => null,
            'materialNode' => $this->getIriFor('materialNode2'),
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'materialNode',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialItemValidatesMissingArticle() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([], ['article'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'article',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialItemAllowsMissingQuantity() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([], ['quantity'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['quantity' => null]);
    }

    public function testCreateMaterialItemValidatesInvalidQuantity() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'quantity' => '1',
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "quantity" attribute must be "float", "string" given.',
        ]);
    }

    public function testCreateMaterialItemAllowsMissingUnit() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([], ['unit'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['unit' => null]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            MaterialItem::class,
            '/material_items',
            'post',
            array_merge([
                'materialList' => $this->getIriFor('materialList1'),
                'period' => $this->getIriFor('period1'),
                'materialNode' => null,
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            MaterialItem::class,
            '/material_items',
            'get',
            $attributes,
            ['materialList', 'period', 'materialNode'],
            $except
        );
    }
}
