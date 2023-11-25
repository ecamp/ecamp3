<?php

namespace App\Tests\Api\ContentNodes\DefaultLayout;

use App\Entity\ContentNode\DefaultLayout;
use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadDefaultLayoutTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/default_layouts';
        $this->defaultEntity = static::$fixtures['defaultLayout1'];
    }

    public function testGetDefaultLayout() {
        // given
        /** @var DefaultLayout $contentNode */
        $contentNode = $this->defaultEntity;

        // when
        $this->get($contentNode);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => $contentNode->data]);
    }
}
