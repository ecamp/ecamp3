<?php

namespace App\Tests\Api\ContentNodes\MaterialNode;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListMaterialNodeTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/material_nodes';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('materialNode1'),
            $this->getIriFor('materialNode2'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('materialNodeCampUnrelated'),
        ];
    }
}
