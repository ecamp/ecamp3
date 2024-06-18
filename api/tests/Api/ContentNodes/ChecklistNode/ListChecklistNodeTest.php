<?php

namespace App\Tests\Api\ContentNodes\ChecklistNode;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListChecklistNodeTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/checklist_nodes';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('checklistNode1'),
            $this->getIriFor('checklistNode3'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('checklistNodeCampUnrelated'),
        ];
    }
}
