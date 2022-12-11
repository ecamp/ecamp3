<?php

namespace App\Tests\Api\ContentNodes\ContentNode;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateContentNodeTest extends ECampApiTestCase {
    public function testCreateContentNodeIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/content_node/content_nodes', ['json' => []]);
        $this->assertResponseStatusCodeSame(405);
    }
}
