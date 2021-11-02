<?php

namespace App\Tests\Api\ContentNodes\MaterialNode;

use App\Tests\Api\ContentNodes\DeleteContentNodeTestCase;

/**
 * @internal
 */
class DeleteMaterialNodeTest extends DeleteContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'material_nodes';
        $this->defaultContentNode = static::$fixtures['materialNode1'];
    }
}
