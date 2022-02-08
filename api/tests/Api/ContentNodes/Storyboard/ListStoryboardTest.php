<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListStoryboardTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/storyboards';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('storyboard1'),
            $this->getIriFor('storyboard2'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('storyboardCampUnrelated'),
        ];
    }

    public function testListStoryboardsFilteredByParent() {
        $response = static::createClientWithCredentials()->request('GET', "{$this->endpoint}?parent=".$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContainsItems($response, [$this->getIriFor('storyboard1')]);
    }
}
