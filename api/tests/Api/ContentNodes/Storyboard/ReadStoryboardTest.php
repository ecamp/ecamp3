<?php

namespace App\Tests\Api\ContentNodes\Storyboard;

use App\Entity\ContentNode\Storyboard;
use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadStoryboardTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/storyboards';
        $this->defaultEntity = static::getFixture('storyboard1');
    }

    public function testGetStoryboard() {
        // given
        /** @var Storyboard $contentNode */
        $storyboard = $this->defaultEntity;

        // when
        $this->get($storyboard);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => $storyboard->data]);
    }
}
