<?php

namespace App\Tests\Api\ContentNodes\MultiSelect;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateMultiSelectTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_selects';
        $this->defaultEntity = static::$fixtures['multiSelect1'];
    }

    public function testPatchMultiSelectAcceptsValidJson() {
        $this->patch($this->defaultEntity, ['data' => [
            'options' => [
                'key1' => ['checked' => false],
                'key3' => ['checked' => false],
            ],
        ]]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'data' => [
                'options' => [
                    'key1' => ['checked' => false],
                    'key2' => ['checked' => true],
                    'key3' => ['checked' => false],
                ],
            ],
        ]);
    }

    public function testPatchMultiSelectRejectsInvalidJson() {
        $this->patch($this->defaultEntity, ['data' => [
            'options' => [
                'key1' => ['checked' => false, 'additionalProperty' => 'dummy'],
            ],
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'data',
                    'message' => 'Provided JSON doesn\'t match required schema (Additional properties not allowed: additionalProperty at #->properties:options->additionalProperties:key1->$ref[#/$defs/option]).',
                ],
            ],
        ]);
    }
}
