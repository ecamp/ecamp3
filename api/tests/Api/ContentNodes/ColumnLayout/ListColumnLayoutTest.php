<?php

namespace App\Tests\Api\ContentNodes\ColumnLayout;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListColumnLayoutTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/column_layouts';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('columnLayout1'),
            $this->getIriFor('columnLayout2'),
            $this->getIriFor('columnLayoutChild1'),
            $this->getIriFor('columnLayout2Child1'),
            $this->getIriFor('columnLayout3'),
            $this->getIriFor('columnLayout4'),
            $this->getIriFor('columnLayout2camp2'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('columnLayout1campUnrelated'),
            $this->getIriFor('columnLayout2campUnrelated'),
        ];

        $this->contentNodesCampPrototypes = [
            $this->getIriFor('columnLayout1campPrototype'),
            $this->getIriFor('columnLayout2campPrototype'),
        ];
    }

    public function testListColumnLayoutsFilteredByParent() {
        $response = static::createClientWithCredentials()->request('GET', '/content_node/column_layouts?parent='.$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, [$this->getIriFor('columnLayoutChild1')]);
    }
}
