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

        $this->endpoint = '/content_node/material_nodes';
        $this->entityClass = MaterialNode::class;
        $this->defaultContentType = static::getFixture('contentTypeMaterial');
    }
}
