<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateStoryboardTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'storyboards';
        $this->defaultContentNode = static::$fixtures['storyboard1'];
    }
}
