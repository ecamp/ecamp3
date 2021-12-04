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
                        'pos' => 0,
                    ],
                    [
                        'translateKey' => 'security',
                        'checked' => false,
                        'pos' => 1,
                    ],
                    [
                        'translateKey' => 'natureAndEnvironment',
                        'checked' => false,
                        'pos' => 2,
                    ],
                    [
                        'translateKey' => 'pioneeringTechnique',
                        'checked' => false,
                        'pos' => 3,
                    ],
                    [
                        'translateKey' => 'campsiteAndSurroundings',
                        'checked' => false,
                        'pos' => 4,
                    ],
                    [
                        'translateKey' => 'preventionAndIntegration',
                        'checked' => false,
                        'pos' => 5,
                    ],
                ],
            ],
        ]);
    }
}
