<?php

namespace App\Tests\Api\ContentNodes\ChecklistNode;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateChecklistNodeTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/checklist_nodes';
        $this->defaultEntity = static::getFixture('checklistNode1');
    }
}
