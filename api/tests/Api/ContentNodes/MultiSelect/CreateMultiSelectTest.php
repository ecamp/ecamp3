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
        $response = $this->create($this->getExampleWritePayload());

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertCount(1, $response->toArray()['_links']['options']);
        $this->assertJsonContains([
            '_embedded' => [
                'options' => [
                    [
                        'translateKey' => 'outdoorTechnique',
                        'checked' => false,
                        'position' => 0,
                    ],
                    [
                        'translateKey' => 'security',
                        'checked' => false,
                        'position' => 1,
                    ],
                    [
                        'translateKey' => 'natureAndEnvironment',
                        'checked' => false,
                        'position' => 2,
                    ],
                    [
                        'translateKey' => 'pioneeringTechnique',
                        'checked' => false,
                        'position' => 3,
                    ],
                    [
                        'translateKey' => 'campsiteAndSurroundings',
                        'checked' => false,
                        'position' => 4,
                    ],
                    [
                        'translateKey' => 'preventionAndIntegration',
                        'checked' => false,
                        'position' => 5,
                    ],
                ],
            ],
        ]);
    }
}
