<?php

namespace App\Tests\Api\ContentNodes\ChecklistNode;

use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadChecklistNodeTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/checklist_nodes';
        $this->defaultEntity = static::getFixture('checklistNode3');
    }
}
