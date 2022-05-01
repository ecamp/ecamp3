<?php

namespace App\Tests\Api\ContentNodes\ContentNode;

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
            'totalItems' => 20,
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
            ['href' => $this->getIriFor('columnLayout2Child1')],
            ['href' => $this->getIriFor('columnLayout3')],
            ['href' => $this->getIriFor('columnLayout4')],
            ['href' => $this->getIriFor('columnLayout5')],
            ['href' => $this->getIriFor('columnLayout1camp2')],
            ['href' => $this->getIriFor('columnLayout2camp2')],
            ['href' => $this->getIriFor('columnLayout1campPrototype')],
            ['href' => $this->getIriFor('columnLayout2campPrototype')],
            ['href' => $this->getIriFor('singleText1')],
            ['href' => $this->getIriFor('singleText2')],
            ['href' => $this->getIriFor('safetyConcept1')],
            ['href' => $this->getIriFor('materialNode1')],
            ['href' => $this->getIriFor('materialNode2')],
            ['href' => $this->getIriFor('storyboard1')],
            ['href' => $this->getIriFor('storyboard2')],
            ['href' => $this->getIriFor('multiSelect1')],
            ['href' => $this->getIriFor('multiSelect2')],
        ], $response->toArray()['_links']['items']);
    }
}
