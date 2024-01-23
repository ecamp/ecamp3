<?php

namespace App\Tests\Api\ContentNodes\ContentNode;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteContentNodeTest extends ECampApiTestCase {
    public function testDeleteContentNodeIsNotAllowed() {
        $contentNode = static::getFixture('columnLayoutChild1');
        static::createClientWithCredentials()->request('DELETE', '/content_nodes/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(405);
    }
}
