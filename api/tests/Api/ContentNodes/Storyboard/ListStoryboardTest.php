<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListStoryboardTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'storyboards';
        $this->contentNodeClass = Storyboard::class;
        $this->defaultContentType = static::$fixtures['contentTypeStoryboard'];

        $this->contentNodesCamp1 = [
            $this->getIriFor('storyboard1'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('storyboardCampUnrelated'),
        ];
    }

    public function testListStoryboardsFilteredByParent() {
        $parent = static::$fixtures['columnLayout1'];
        $response = static::createClientWithCredentials()->request('GET', "/content_node/{$this->endpoint}?parent=".$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContainsItems($response, [$this->getIriFor('storyboard1')]);
    }
}
