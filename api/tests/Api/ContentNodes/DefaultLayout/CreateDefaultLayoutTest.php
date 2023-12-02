<?php

namespace App\Tests\Api\ContentNodes\DefaultLayout;

use App\Entity\ContentNode\DefaultLayout;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateDefaultLayoutTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/default_layouts';
        $this->entityClass = DefaultLayout::class;
        $this->defaultContentType = static::$fixtures['contentTypeDefaultLayout'];
    }

    public function testCreateDefaultLayoutAcceptsValidJson() {
        $LAYOUT_JSON_CONFIG = [
            ['slot' => 'main'],
            ['slot' => 'aside-top'],
            ['slot' => 'aside-bottom'],
        ];

        $this->create($this->getExampleWritePayload(['data' => ['items' => $LAYOUT_JSON_CONFIG]]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'items' => $LAYOUT_JSON_CONFIG,
        ]]);
    }

    public function testCreateDefaultLayoutAcceptsEmptyJson() {
        $this->create($this->getExampleWritePayload(['data' => null]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'items' => [['slot' => 'main'], ['slot' => 'aside-top'], ['slot' => 'aside-bottom']],
        ]]);
    }

    public function testCreateDefaultLayoutAcceptsNonExistingJson() {
        $response = $this->create($this->getExampleWritePayload([], ['data']));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'items' => [['slot' => 'main'], ['slot' => 'aside-top'], ['slot' => 'aside-bottom']],
        ]]);
    }

    public function testCreateDefaultLayoutRejectsInvalidJson() {
        $INVALID_JSON_CONFIG = [
            'data' => 'value',
        ];

        $response = $this->create($this->getExampleWritePayload(['data' => ['items' => $INVALID_JSON_CONFIG]]));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }

    /**
     * payload set up.
     */
    public function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge(
                [
                    'data' => [
                        'items' => [
                            ['slot' => 'main'],
                            ['slot' => 'aside-top'],
                            ['slot' => 'aside-bottom'],
                        ],
                    ],
                ],
                $attributes
            ),
            $except
        );
    }
}
