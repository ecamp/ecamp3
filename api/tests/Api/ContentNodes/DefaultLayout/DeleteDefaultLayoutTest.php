<?php

namespace App\Tests\Api\ContentNodes\DefaultLayout;

use App\Tests\Api\ContentNodes\DeleteContentNodeTestCase;

/**
 * @internal
 */
class DeleteDefaultLayoutTest extends DeleteContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/default_layouts';
        $this->defaultEntity = static::$fixtures['defaultLayout1'];
    }
}
