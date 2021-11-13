<?php

namespace App\Tests\Api\ContentNodes\MultiSelect;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListMultiSelectTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_selects';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('multiSelect1'),
            $this->getIriFor('multiSelect2'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('multiSelectCampUnrelated'),
        ];
    }

    public function testListMultiSelectsFilteredByParent() {
        $response = static::createClientWithCredentials()->request('GET', "{$this->endpoint}?parent=".$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContainsItems($response, [$this->getIriFor('multiSelect1')]);
    }
}
