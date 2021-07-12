<?php

namespace App\Tests\Api\ContentNodes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListContentNodesTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListContentNodesIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 6,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('contentNode1')],
            ['href' => $this->getIriFor('contentNode2')],
            ['href' => $this->getIriFor('contentNodeChild1')],
            ['href' => $this->getIriFor('contentNodeChild2')],
            ['href' => $this->getIriFor('contentNodeGrandchild1')],
            ['href' => $this->getIriFor('contentNode1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentNodesFilteredByParentIsAllowedForCollaborator() {
        $parent = static::$fixtures['contentNode1'];
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
            ['href' => $this->getIriFor('contentNodeChild2')],
        ], $response->toArray()['_links']['items']);
    }
}
