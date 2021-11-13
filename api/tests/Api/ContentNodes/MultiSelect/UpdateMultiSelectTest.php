<?php

namespace App\Tests\Api\ContentNodes\MultiSelect;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateMultiSelectTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_selects';
        $this->defaultEntity = static::$fixtures['multiSelect1'];
    }
}
