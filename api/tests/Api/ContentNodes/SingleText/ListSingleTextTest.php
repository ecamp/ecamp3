<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListSingleTextTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/single_texts';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('singleText1'),
            $this->getIriFor('singleText2'),
            $this->getIriFor('safetyConcept1'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('singleTextCampUnrelated'),
        ];
    }
}
