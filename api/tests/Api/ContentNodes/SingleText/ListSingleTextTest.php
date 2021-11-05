<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListSingleTextTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/single_texts';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('singleText1'),
            $this->getIriFor('singleText2'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('singleTextCampUnrelated'),
        ];
    }

    public function testListSingleTextsFilteredByParent() {
        $response = static::createClientWithCredentials()->request('GET', "{$this->endpoint}?parent=".$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);
    }
}
