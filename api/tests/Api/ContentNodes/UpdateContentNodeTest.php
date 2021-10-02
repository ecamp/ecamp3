<?php

namespace App\Tests\Api\ContentNodes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateContentNodeTest extends ECampApiTestCase {
    public function testPatchContentNodeIsAllowedForCollaborator() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), [
            'json' => [],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
        $this->assertResponseStatusCodeSame(405);
    }
}
