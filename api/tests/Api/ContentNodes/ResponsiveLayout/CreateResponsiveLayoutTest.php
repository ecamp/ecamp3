<?php

namespace App\Tests\Api\ContentNodes\ResponsiveLayout;

use App\Entity\ContentNode\ResponsiveLayout;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateResponsiveLayoutTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/responsive_layouts';
        $this->entityClass = ResponsiveLayout::class;
        $this->defaultContentType = static::$fixtures['contentTypeResponsiveLayout'];
    }

    public function testCreateResponsiveLayoutAcceptsValidJson() {
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

    public function testCreateResponsiveLayoutAcceptsEmptyJson() {
        $this->create($this->getExampleWritePayload(['data' => null]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'items' => [['slot' => 'main'], ['slot' => 'aside-top'], ['slot' => 'aside-bottom']],
        ]]);
    }

    public function testCreateResponsiveLayoutAcceptsNonExistingJson() {
        $response = $this->create($this->getExampleWritePayload([], ['data']));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'items' => [['slot' => 'main'], ['slot' => 'aside-top'], ['slot' => 'aside-bottom']],
        ]]);
    }

    public function testCreateResponsiveLayoutRejectsInvalidJson() {
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
