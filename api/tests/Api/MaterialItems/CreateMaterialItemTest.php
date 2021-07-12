<?php

namespace App\Tests\Api\MaterialItems;

use App\Entity\MaterialItem;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateMaterialItemTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateMaterialItemIsAllowedForCollaborator() {
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

    public function testCreateMaterialItemWithContentNodeInsteadOfPeriodIsPossible() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'contentNode' => $this->getIriFor('contentNode1'),
            'period' => null,
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            '_links' => [
                'contentNode' => ['href' => $this->getIriFor('contentNode1')],
                //'period' => null,
            ],
        ]));
    }

    public function testCreateMaterialItemValidatesMissingPeriodAndContentNode() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([], ['period', 'contentNode'])]);

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

    public function testCreateMaterialItemValidatesConflictingPeriodAndContentNode() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'period' => $this->getIriFor('period1'),
            'contentNode' => $this->getIriFor('contentNode1'),
        ])]);

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

    public function testCreateMaterialItemValidatesPeriodFromDifferentCamp() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'period' => $this->getIriFor('period1camp2'),
            'contentNode' => null,
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

    public function testCreateMaterialItemValidatesContentNodeFromDifferentCamp() {
        static::createClientWithCredentials()->request('POST', '/material_items', ['json' => $this->getExampleWritePayload([
            'period' => null,
            'contentNode' => $this->getIriFor('contentNode1camp2'),
        ])]);

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
            array_merge([
                'materialList' => $this->getIriFor('materialList1'),
                'period' => $this->getIriFor('period1'),
                'contentNode' => null,
            ], $attributes),
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            MaterialItem::class,
            $attributes,
            array_merge(['materialList', 'period', 'contentNode'], $except)
        );
    }
}
