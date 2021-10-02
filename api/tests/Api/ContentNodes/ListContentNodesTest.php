<?php

namespace App\Tests\Api\ContentNodes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListContentNodesTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function setUp(): void {
        $this->markTestSkipped('Tests temporarily inactive (rewritings tests TBD)');
    }

    public function testListContentNodesIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 13,
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
            ['href' => $this->getIriFor('contentNodeChild1')],
            ['href' => $this->getIriFor('singleText1')],
            ['href' => $this->getIriFor('singleText2')],
            ['href' => $this->getIriFor('columnLayout3')],
            ['href' => $this->getIriFor('columnLayout4')],
            ['href' => $this->getIriFor('materialNode2')],
            ['href' => $this->getIriFor('columnLayout2camp2')],
            ['href' => $this->getIriFor('columnLayout1campPrototype')],
            ['href' => $this->getIriFor('columnLayout2campPrototype')],
            // The next two should not be visible once we implement proper entity filtering for content nodes
            ['href' => $this->getIriFor('columnLayout1campUnrelated')],
            ['href' => $this->getIriFor('columnLayout2campUnrelated')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentNodesFilteredByParentIsAllowedForCollaborator() {
        $parent = static::$fixtures['columnLayout1'];
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes?parent=/content_nodes/'.$parent->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 2,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('contentNodeChild1')],
            ['href' => $this->getIriFor('singleText1')],
        ], $response->toArray()['_links']['items']);
    }
}
