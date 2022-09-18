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
        $this->defaultContentType = static::$fixtures['contentTypeColumnLayout'];
    }

    /**
     * tests specific for Columnlayout.
     */
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
            'columns' => [['slot' => '1', 'width' => 12]],
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
     * tests common to all ContentNodes (tested only here).
     */
    public function testCreateColumnLayoutSetsRootToParentsRoot() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'root' => ['href' => $this->getIriFor(static::$fixtures['columnLayout1'])],
        ]]);
    }

    public function testCreateColumnLayoutValidatesMissingParent() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['parent'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateColumnLayoutAllowsMissingSlot() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['slot'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['slot' => null]);
    }

    public function testCreateColumnLayoutAllowsMissingPosition() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['position'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['position' => 0]);
    }

    public function testCreateColumnLayoutAllowsMissingInstanceName() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['instanceName'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['instanceName' => null]);
    }

    public function testCreateColumnLayoutValidatesMissingContentType() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['contentType'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'contentType',
                    'message' => 'This value should not be null.',
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
