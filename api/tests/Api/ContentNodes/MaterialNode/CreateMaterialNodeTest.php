<?php

namespace App\Tests\Api\ContentNodes\MaterialNode;

use App\Entity\ContentNode\MaterialNode;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateMaterialNodeTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'material_nodes';
        $this->contentNodeClass = MaterialNode::class;
        $this->defaultContentType = static::$fixtures['contentTypeMaterial'];
    }

    public function testCreateMaterialNodeFromPrototype() {
        // given
        $prototype = static::$fixtures['materialNode1'];
        $prototypeItem = static::$fixtures['materialItem1'];

        // when
        $response = $this->create($this->getExampleWritePayload(['prototype' => $this->getIriFor('materialNode1')]));

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertCount(1, $response->toArray()['_links']['materialItems']);
        $this->assertJsonContains([
            '_embedded' => [
                'materialItems' => [
                    [
                        'article' => $prototypeItem->article,
                        'quantity' => $prototypeItem->quantity,
                        'unit' => $prototypeItem->unit,
                        'materialList' => $prototypeItem->materialList,
                    ],
                ],
            ],
        ]);
    }
}
