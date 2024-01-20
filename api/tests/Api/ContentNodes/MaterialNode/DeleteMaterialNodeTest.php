<?php

namespace App\Tests\Api\ContentNodes\MaterialNode;

use App\Tests\Api\ContentNodes\DeleteContentNodeTestCase;

/**
 * @internal
 */
class DeleteMaterialNodeTest extends DeleteContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/material_nodes';
        $this->defaultEntity = static::getFixture('materialNode1');
    }
}
