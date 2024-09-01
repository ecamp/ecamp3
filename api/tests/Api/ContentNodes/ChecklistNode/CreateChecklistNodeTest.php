<?php

namespace App\Tests\Api\ContentNodes\ChecklistNode;

use App\Entity\ContentNode\ChecklistNode;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateChecklistNodeTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/checklist_nodes';
        $this->entityClass = ChecklistNode::class;
        $this->defaultContentType = static::getFixture('contentTypeChecklist');
    }

    /**
     * payload set up.
     */
    public function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            $attributes,
            array_merge(['addChecklistItemIds', 'removeChecklistItemIds'], $except)
        );
    }
}
