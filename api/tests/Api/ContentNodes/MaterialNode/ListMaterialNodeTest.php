<?php

namespace App\Tests\Api\ContentNodes\MaterialNode;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListMaterialNodeTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'material_nodes';
        $this->contentNodeClass = MaterialNode::class;
        $this->defaultContentType = static::$fixtures['contentTypeNotes'];

        $this->contentNodesCamp1 = [
            $this->getIriFor('materialNode1'),
            $this->getIriFor('materialNode2'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('materialNodeCampUnrelated'),
        ];
    }

    public function testListMaterialNodesFilteredByParent() {
        $parent = static::$fixtures['columnLayout1'];
        $response = static::createClientWithCredentials()->request('GET', "/content_node/{$this->endpoint}?parent=".$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContainsItems($response, [$this->getIriFor('materialNode1')]);
    }
}
