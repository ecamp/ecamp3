<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteContentNodeTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function setUp(): void {
        $this->markTestSkipped('Tests temporarily inactive (rewritings tests TBD)');
    }

    public function testDeleteContentNodeIsAllowedForCollaborator() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('DELETE', '/content_nodes/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ContentNode::class)->find($contentNode->getId()));
    }

    public function testDeleteContentNodeIsNotAllowedWhenContentNodeIsRoot() {
        $contentNode = static::$fixtures['contentNode1'];
        static::createClientWithCredentials()->request('DELETE', '/content_nodes/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
