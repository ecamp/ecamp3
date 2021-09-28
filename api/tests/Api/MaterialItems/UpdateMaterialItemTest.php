<?php

namespace App\Tests\Api\MaterialItems;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateMaterialItemTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testPatchMaterialItemIsDeniedForAnonymousUser() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createBasicClient()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialList' => $this->getIriFor('materialList2'),
            'period' => $this->getIriFor('period1'),
            'contentNode' => null,
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchMaterialItemIsDeniedForUnrelatedUser() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
                'materialList' => $this->getIriFor('materialList2'),
                'period' => $this->getIriFor('period1'),
                'contentNode' => null,
                'article' => 'Mehl',
                'quantity' => 1500,
                'unit' => 'g',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchMaterialItemIsDeniedForInactiveCollaborator() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
                'materialList' => $this->getIriFor('materialList2'),
                'period' => $this->getIriFor('period1'),
                'contentNode' => null,
                'article' => 'Mehl',
                'quantity' => 1500,
                'unit' => 'g',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchMaterialItemIsDeniedForGuest() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
                'materialList' => $this->getIriFor('materialList2'),
                'period' => $this->getIriFor('period1'),
                'contentNode' => null,
                'article' => 'Mehl',
                'quantity' => 1500,
                'unit' => 'g',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchMaterialItemIsAllowedForMember() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
                'materialList' => $this->getIriFor('materialList2'),
                'period' => $this->getIriFor('period1'),
                'contentNode' => null,
                'article' => 'Mehl',
                'quantity' => 1500,
                'unit' => 'g',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
            '_links' => [
                'materialList' => ['href' => $this->getIriFor('materialList2')],
                'period' => ['href' => $this->getIriFor('period1')],
                //'contentNode' => null,
            ],
        ]);
    }

    public function testPatchMaterialItemIsAllowedForManager() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialList' => $this->getIriFor('materialList2'),
            'period' => $this->getIriFor('period1'),
            'contentNode' => null,
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
            '_links' => [
                'materialList' => ['href' => $this->getIriFor('materialList2')],
                'period' => ['href' => $this->getIriFor('period1')],
                //'contentNode' => null,
            ],
        ]);
    }

    public function testPatchMaterialItemInCampPrototypeIsDeniedForUnrelatedUser() {
        $materialItem = static::$fixtures['materialItem1period1campPrototype'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'contentNode' => null,
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchMaterialItemValidatesMissingMaterialList() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialList' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Expected IRI or nested document for attribute "materialList", "NULL" given.',
        ]);
    }

    public function testPatchMaterialItemValidatesMaterialListFromDifferentCamp() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialList' => $this->getIriFor('materialList1camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'materialList',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemAllowsPeriodInsteadOfContentNode() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'period' => $this->getIriFor('period1'),
            'contentNode' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                //'contentNode' => null,
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesMissingPeriodAndContentNode() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'period' => null,
            'contentNode' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Either this value or contentNode should not be null.',
                ],
                [
                    'propertyPath' => 'contentNode',
                    'message' => 'Either this value or period should not be null.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesConflictingPeriodAndContentNode() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'period' => $this->getIriFor('period1'),
            'contentNode' => $this->getIriFor('contentNode1'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Either this value or contentNode should be null.',
                ],
                [
                    'propertyPath' => 'contentNode',
                    'message' => 'Either this value or period should be null.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesPeriodFromDifferentCamp() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'period' => $this->getIriFor('period1camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

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

    public function testPatchMaterialItemValidatesContentNodeFromDifferentCamp() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'contentNode' => $this->getIriFor('contentNode1camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'contentNode',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesMissingArticle() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'article' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "article" attribute must be "string", "NULL" given.',
        ]);
    }

    public function testPatchMaterialItemAllowsMissingQuantity() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'quantity' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['quantity' => null]);
    }

    public function testPatchMaterialItemValidatesInvalidQuantity() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'quantity' => '1',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "quantity" attribute must be "float", "string" given.',
        ]);
    }

    public function testPatchMaterialItemAllowsMissingUnit() {
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'unit' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['unit' => null]);
    }
}
