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

        $this->endpoint = '/multi_selects';
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

    public function testCreateDoesNotAcceptOptions() {
        $this->create($this->getExampleWritePayload([
            'data' => [
                'options' => [
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                0 => [
                    'propertyPath' => 'data',
                    'message' => 'This value should be null.',
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
