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
                        'translateKey' => 'option1',
                        'checked' => false,
                        'pos' => 0,
                    ],
                    [
                        'translateKey' => 'option2',
                        'checked' => false,
                        'pos' => 1,
                    ],
                    [
                        'translateKey' => 'option3',
                        'checked' => false,
                        'pos' => 2,
                    ],
                ],
            ],
        ]);
    }

    public function testCreateMultiSelectFromPrototype() {
        // given
        $prototype = static::$fixtures['multiSelect1'];
        $prototypeOption = static::$fixtures['multiSelectOption1'];

        // when
        $response = $this->create($this->getExampleWritePayload(['prototype' => $this->getIriFor('multiSelect1')]));

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertCount(1, $response->toArray()['_links']['options']);
        $this->assertJsonContains([
            '_embedded' => [
                'options' => [
                    [
                        'translateKey' => $prototypeOption->translateKey,
                        'checked' => $prototypeOption->checked,
                        'pos' => $prototypeOption->getPos(),
                    ],
                ],
            ],
        ]);
    }
}
