<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Tests\Api\ContentNodes\DeleteContentNodeTestCase;

/**
 * @internal
 */
class DeleteStoryboardTest extends DeleteContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'storyboards';
        $this->defaultContentNode = static::$fixtures['storyboard1'];
    }
}
