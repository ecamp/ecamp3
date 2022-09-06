<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Entity\ContentNode\SingleText;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateSingleTextTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/single_texts';
        $this->entityClass = SingleText::class;
        $this->defaultContentType = static::$fixtures['contentTypeNotes'];
    }

    public function testCreateSingleTextFromString() {
        // given
        $text = 'TestText';

        // when
        $this->create($this->getExampleWritePayload(['data' => ['text' => $text]]));

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => ['text' => $text]]);
    }

    public function testCreateSingleTextAcceptsEmptyJson() {
        // when
        $this->create($this->getExampleWritePayload(['data' => null]));

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => ['text' => '']]);
    }

    public function testCreateSingleTextCleansHTMLFromText() {
        // given
        $text = ' testText<script>alert(1)</script>';

        // when
        $this->create($this->getExampleWritePayload(['data' => ['text' => $text]]));

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['data' => [
            'text' => ' testText',
        ]]);
    }
}
