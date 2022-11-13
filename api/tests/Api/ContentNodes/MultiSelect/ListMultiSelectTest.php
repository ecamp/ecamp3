<?php

namespace App\Tests\Api\ContentNodes\MultiSelect;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListMultiSelectTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/multi_selects';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('multiSelect1'),
            $this->getIriFor('multiSelect2'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('multiSelectCampUnrelated'),
        ];
    }
}
