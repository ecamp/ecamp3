<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListSingleTextTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'single_texts';
        $this->contentNodeClass = SingleText::class;
        $this->defaultContentType = static::$fixtures['contentTypeNotes'];

        $this->contentNodesCamp1 = [
            $this->getIriFor('singleText1'),
            $this->getIriFor('singleText2'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('singleTextCampUnrelated'),
        ];
    }

    public function testListSingleTextsFilteredByParent() {
        $parent = static::$fixtures['columnLayout1'];
        $response = static::createClientWithCredentials()->request('GET', "/content_node/{$this->endpoint}?parent=".$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContainsItems($response, [$this->getIriFor('singleText1')]);
    }
}
