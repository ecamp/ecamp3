<?php

namespace App\Tests\Api\ContentNodes\MaterialNode;

use App\Entity\ContentNode\MaterialNode;
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

        // when
        $this->get($contentNode);

        // then
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            '_links' => [
                'materialItems' => [
                    ['href' => $this->getIriFor('materialItem1')],
                ],
            ],
            '_embedded' => [
                'materialItems' => [
                    ['href' => $this->getIriFor('materialItem1')],
                ],
            ],
        ]);
    }
}
