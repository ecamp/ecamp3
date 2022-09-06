<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Entity\ContentNode\Storyboard;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateStoryboardTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/storyboards';
        $this->entityClass = Storyboard::class;
        $this->defaultContentType = static::$fixtures['contentTypeStoryboard'];
    }

    public function testCreateStoryboardAcceptsValidJson() {
        $this->create($this->getExampleWritePayload(['data' => [
            'sections' => [
                'f5ee1e2a-af0a-4fa5-8f3f-b869ed184c5c' => [
                    'column1' => 'A',
                    'column2' => 'B',
                    'column3' => ' testText<script>alert(1)</script>',
                    'position' => 99,
                ],
            ],
        ]]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'sections' => [
                'f5ee1e2a-af0a-4fa5-8f3f-b869ed184c5c' => [
                    'column1' => 'A',
                    'column2' => 'B',
                    'column3' => ' testText',
                    'position' => 99,
                ],
            ],
        ]]);
    }

    public function testCreateStoryboardAcceptsEmptyJson() {
        $this->create($this->getExampleWritePayload(['data' => null]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'sections' => [],
        ]]);
    }

    public function testCreateStoryboardRejectsInvalidJson() {
        $response = $this->create($this->getExampleWritePayload(['data' => ['sections' => 'dummy']]));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }
}
