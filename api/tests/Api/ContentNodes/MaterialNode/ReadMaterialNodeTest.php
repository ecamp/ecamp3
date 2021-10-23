<?php

namespace App\Tests\Api\ContentNodes\MaterialNode;

use App\Entity\ContentNode\MaterialNode;
use App\Entity\MaterialItem;
use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadMaterialNodeTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'material_nodes';
        $this->defaultContentNode = static::$fixtures['materialNode1'];
    }

    public function testGetMaterialNode() {
        // given
        /** @var MaterialNode $contentNode */
        $contentNode = $this->defaultContentNode;

        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1'];

        // when
        $this->get($contentNode);

        // then
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            '_links' => [
                'materialItems' => [
                    ['href' => $this->getIriFor($materialItem)],
                ],
            ],
            '_embedded' => [
                'materialItems' => [
                    [
                        'article' => $materialItem->article,
                        'quantity' => (int) $materialItem->quantity,
                        'unit' => $materialItem->unit,
                        'id' => $materialItem->getId(),
                        '_links' => [
                            'period' => null,
                            'materialList' => ['href' => $this->getIriFor($materialItem->materialList)],
                            'materialNode' => ['href' => $this->getIriFor($contentNode)],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
