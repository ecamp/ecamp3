<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadContentNodeTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function setUp(): void {
        $this->markTestSkipped('Tests temporarily inactive (rewritings tests TBD)');
    }

    public function testGetSingleContentNodeIsAllowedForCollaborator() {
        /** @var ContentNode $contentNode */
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('GET', '/content_nodes/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $contentNode->getId(),
            'instanceName' => $contentNode->instanceName,
            'slot' => $contentNode->slot,
            'position' => $contentNode->position,
            'contentTypeName' => $contentNode->getContentTypeName(),
            'jsonConfig' => $contentNode->jsonConfig,
            '_links' => [
                'parent' => ['href' => $this->getIriFor($contentNode->parent)],
                'owner' => ['href' => $this->getIriFor('activity1')],
                'ownerCategory' => ['href' => $this->getIriFor('category1')],
                'children' => ['href' => '/content_nodes?parent=/content_nodes/'.$contentNode->getId()],
            ],
        ]);
    }
}
