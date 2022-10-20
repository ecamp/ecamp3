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
        $response = $this->create($this->getExampleWritePayload(['data' => [
            'sections' => [
                'f5ee1e2a-af0a-4fa5-8f3f-b869ed184c5c' => [
                    'column1' => " testText\n\t",
                    'column2Html' => ' <b>testText</b><script>alert(1)</script>',
                    'column3' => " testText\n\t",
                    'position' => 99,
                ],
            ],
        ]]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertCount(1, $response->toArray()['data']['sections']);
        $this->assertJsonContains(['data' => [
            'sections' => [
                'f5ee1e2a-af0a-4fa5-8f3f-b869ed184c5c' => [
                    'column1' => ' testText',
                    'column2Html' => ' <b>testText</b>',
                    'column3' => ' testText',
                    'position' => 99,
                ],
            ],
        ]]);
    }

    public function testCreateStoryboardAcceptsEmptyJson() {
        $response = $this->create($this->getExampleWritePayload(['data' => null]));

        $this->assertResponseStatusCodeSame(201);

        $this->assertCount(1, $response->toArray()['data']['sections']); // populated with 1 default section
    }

    public function testCreateStoryboardAcceptsNotExistingJson() {
        $response = $this->create($this->getExampleWritePayload([], ['data']));

        $this->assertResponseStatusCodeSame(201);

        $this->assertCount(1, $response->toArray()['data']['sections']); // populated with 1 default section
    }

    public function testCreateStoryboardRejectsInvalidJson() {
        $response = $this->create($this->getExampleWritePayload(['data' => ['sections' => 'dummy']]));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }
}
