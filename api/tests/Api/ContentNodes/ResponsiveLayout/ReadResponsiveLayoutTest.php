<?php

namespace App\Tests\Api\ContentNodes\ResponsiveLayout;

use App\Entity\ContentNode\ResponsiveLayout;
use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadResponsiveLayoutTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/responsive_layouts';
        $this->defaultEntity = static::$fixtures['responsiveLayout1'];
    }

    public function testGetResponsiveLayout() {
        // given
        /** @var ResponsiveLayout $contentNode */
        $contentNode = $this->defaultEntity;

        // when
        $this->get($contentNode);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => $contentNode->data]);
    }
}
