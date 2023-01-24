<?php

namespace App\Tests\Api\ContentNodes\ContentNode;

use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadContentNodeTest extends ECampApiTestCase {
    public function testGetSingleContentNodeIsNotImplemented() {
        // given
        /** @var ContentNode $contentNode */
        $contentNode = static::$fixtures['columnLayoutChild1'];

        // when (requesting with anonymous user)
        static::createClientWithCredentials()->request('GET', '/content_nodes/'.$contentNode->getId());

        // then
        $this->assertResponseStatusCodeSame(404);
    }
}
