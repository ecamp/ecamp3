<?php

namespace App\Tests\Api\ContentNodes\MaterialNode;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateMaterialNodeTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'material_nodes';
        $this->defaultContentNode = static::$fixtures['materialNode1'];
    }
}
