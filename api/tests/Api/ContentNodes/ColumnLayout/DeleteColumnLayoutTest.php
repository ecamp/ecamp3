<?php

namespace App\Tests\Api\ContentNodes\ColumnLayout;

use App\Tests\Api\ContentNodes\DeleteContentNodeTestCase;

/**
 * @internal
 */
class DeleteColumnLayoutTest extends DeleteContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/column_layouts';
        $this->defaultEntity = static::$fixtures['columnLayoutChild1'];
    }

    public function testDeleteColumnLayoutIsNotAllowedWhenColumnLayoutIsRoot() {
        // when
        $this->delete(entity: static::$fixtures['columnLayout1']);

        // then
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
