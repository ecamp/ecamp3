<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateSingleTextTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/single_texts';
        $this->defaultEntity = static::$fixtures['singleText1'];
    }

    public function testPatchText() {
        // when
        $this->patch($this->defaultEntity, ['data' => ['html' => 'testText']]);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => [
            'html' => 'testText',
        ]]);
    }

    public function testPatchCleansHTMLFromText() {
        // when
        $this->patch($this->defaultEntity, ['data' => ['html' => ' testText<script>alert(1)</script>']]);

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => [
            'html' => ' testText',
        ]]);
    }
}
