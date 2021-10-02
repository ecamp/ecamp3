<?php

namespace App\Tests\Api\ContentNodes\ColumnLayout;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListColumnLayoutTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListColumnLayouts() {
        $response = static::createClientWithCredentials()->request('GET', '/content_node/column_layouts');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 10,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('columnLayout1')],
            ['href' => $this->getIriFor('columnLayout2')],
            ['href' => $this->getIriFor('columnLayoutChild1')],
            ['href' => $this->getIriFor('columnLayout3')],
            ['href' => $this->getIriFor('columnLayout4')],
            ['href' => $this->getIriFor('columnLayout2camp2')],
            ['href' => $this->getIriFor('columnLayout1campPrototype')],
            ['href' => $this->getIriFor('columnLayout2campPrototype')],
            // The next two should not be visible once we implement proper entity filtering for content nodes
            ['href' => $this->getIriFor('columnLayout1campUnrelated')],
            ['href' => $this->getIriFor('columnLayout2campUnrelated')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListColumnLayoutsFilteredByParent() {
        $parent = static::$fixtures['columnLayout1'];
        $response = static::createClientWithCredentials()->request('GET', '/content_node/column_layouts?parent='.$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('columnLayoutChild1')],
        ], $response->toArray()['_links']['items']);
    }
}
