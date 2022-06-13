<?php

namespace App\Tests\Api\ContentNodes\ColumnLayout;

use App\Entity\ContentNode\ColumnLayout;
use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadColumnLayoutTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/column_layouts';
        $this->defaultEntity = static::$fixtures['columnLayoutChild1'];
    }

    public function testGetColumnLayout() {
        // given
        /** @var ColumnLayout $contentNode */
        $contentNode = $this->defaultEntity;

        // when
        $this->get($contentNode);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'columns' => $contentNode->columns,
            '_links' => [
                'children' => [],
            ],
        ]);
    }

    public function testGetColumnLayoutInCategory() {
        // given
        /** @var ColumnLayout $contentNode */
        $contentNode = static::$fixtures['columnLayout2']; // root content node of category1

        // when
        $this->get($contentNode);

        // then
        $this->assertResponseStatusCodeSame(200);
    }
}
