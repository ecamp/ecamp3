<?php

namespace App\Tests\Api\ContentNodes\MultiSelect;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateMultiSelectTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/multi_selects';
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
        $response = $this->patch($this->defaultEntity, ['data' => [
            'options' => [
                'key1' => ['checked' => false, 'additionalProperty' => 'dummy'],
            ],
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }

    public function testPatchMultiSelectDoesNotSetDataToNull() {
        $this->patch($this->defaultEntity, ['data' => null]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'data' => [
                'options' => [
                    'key1' => ['checked' => true],
                    'key2' => ['checked' => true],
                ],
            ],
        ]);
    }

    public function testPatchMultiSelectDoesNotSetDataToEmptyArray() {
        $this->patch($this->defaultEntity, ['data' => []]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'data' => [
                'options' => [
                    'key1' => ['checked' => true],
                    'key2' => ['checked' => true],
                ],
            ],
        ]);
    }

    /**
     * Because the empty array is not a valid JSON Object.
     */
    public function testPatchMultiSelectAccidentallyDoesNotAcceptEmptyOptions() {
        $response = $this->patch($this->defaultEntity, ['data' => [
            'options' => [],
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }
}
