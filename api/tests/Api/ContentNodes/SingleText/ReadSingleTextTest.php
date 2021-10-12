<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Entity\ContentNode\SingleText;
use App\Tests\Api\ContentNodes\ReadContentNodeTestCase;

/**
 * @internal
 */
class ReadSingleTextTest extends ReadContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = 'single_texts';
        $this->defaultContentNode = static::$fixtures['singleText1'];
    }

    public function testGetSingleText() {
        // given
        /** @var SingleText $contentNode */
        $contentNode = $this->defaultContentNode;

        // when
        $this->get($contentNode);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'text' => $contentNode->text,
        ]);
    }
}
