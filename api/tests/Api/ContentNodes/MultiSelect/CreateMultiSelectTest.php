<?php

namespace App\Tests\Api\ContentNodes\MultiSelect;

use App\Entity\ContentNode\MultiSelect;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateMultiSelectTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_selects';
        $this->entityClass = MultiSelect::class;
        $this->defaultContentType = static::$fixtures['contentTypeMultiSelect'];
    }

    public function testCreateMultiSelectCopiesOptionsFromContentType() {
        // when
        $this->create($this->getExampleWritePayload());

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'data' => [
                'options' => [
                    'outdoorTechnique' => ['checked' => false],
                    'security' => ['checked' => false],
                    'natureAndEnvironment' => ['checked' => false],
                    'pioneeringTechnique' => ['checked' => false],
                    'campsiteAndSurroundings' => ['checked' => false],
                    'preventionAndIntegration' => ['checked' => false],
                ],
            ],
        ]);
    }

    protected function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge([
                'data' => null,
            ], $attributes),
            $except
        );
    }
}
