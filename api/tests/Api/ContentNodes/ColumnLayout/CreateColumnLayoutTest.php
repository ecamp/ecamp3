<?php

namespace App\Tests\Api\ContentNodes\ColumnLayout;

use App\Entity\ContentNode\ColumnLayout;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateColumnLayoutTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/column_layouts';
        $this->entityClass = ColumnLayout::class;
        $this->defaultContentType = static::getFixture('contentTypeColumnLayout');
    }

    public function testCreateColumnLayoutAcceptsValidJson() {
        $SINGLE_COLUMN_JSON_CONFIG = [
            ['slot' => '1', 'width' => 5],
            ['slot' => '2', 'width' => 7],
        ];

        $this->create($this->getExampleWritePayload(['data' => ['columns' => $SINGLE_COLUMN_JSON_CONFIG]]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'columns' => $SINGLE_COLUMN_JSON_CONFIG,
        ]]);
    }

    public function testCreateColumnLayoutAcceptsEmptyJson() {
        $this->create($this->getExampleWritePayload(['data' => null]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'columns' => [['slot' => '1', 'width' => 6], ['slot' => '2', 'width' => 6]],
        ]]);
    }

    public function testCreateColumnLayoutAcceptsNonExistingJson() {
        $response = $this->create($this->getExampleWritePayload([], ['data']));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'columns' => [['slot' => '1', 'width' => 6], ['slot' => '2', 'width' => 6]],
        ]]);
    }

    public function testCreateColumnLayoutRejectsInvalidJson() {
        $INVALID_JSON_CONFIG = [
            'data' => 'value',
        ];

        $response = $this->create($this->getExampleWritePayload(['data' => ['columns' => $INVALID_JSON_CONFIG]]));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }

    public function testCreateColumnLayoutRejectsInvalidWidth() {
        $JSON_CONFIG = [
            ['slot' => '1', 'width' => 6],
            ['slot' => '2', 'width' => 5],
        ];

        $this->create($this->getExampleWritePayload(['data' => ['columns' => $JSON_CONFIG]]));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'data',
                    'message' => 'Expected column widths to sum to 12, but got a sum of 11',
                ],
            ],
        ]);
    }

    /**
     * payload set up.
     */
    public function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge(
                [
                    'data' => [
                        'columns' => [['slot' => '1', 'width' => 12]],
                    ],
                ],
                $attributes
            ),
            $except
        );
    }
}
