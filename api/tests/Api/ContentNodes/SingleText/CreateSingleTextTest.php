<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\ContentNode\SingleText;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateSingleTextTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateSingleTextFromString() {
        $text = 'TestText';
        static::createClientWithCredentials()->request('POST', '/content_node/single_texts', ['json' => $this->getExampleWritePayload(['text' => $text])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
        $this->assertJsonContains(['text' => $text]);
    }

    public function testCreateSingleTextFromNull() {
        static::createClientWithCredentials()->request('POST', '/content_node/single_texts', ['json' => $this->getExampleWritePayload(['text' => null])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
        $this->assertJsonContains(['text' => null]);
    }

    public function testCreateSingleTextFromPrototype() {
        $prototype = static::$fixtures['singleText2'];
        static::createClientWithCredentials()->request('POST', '/content_node/single_texts', ['json' => $this->getExampleWritePayload(['prototype' => $this->getIriFor('singleText2')])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
        $this->assertJsonContains(['text' => $prototype->text]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            SingleText::class,
            OperationType::COLLECTION,
            'post',
            array_merge([
                'parent' => $this->getIriFor('columnLayout1'),
                'contentType' => $this->getIriFor('contentTypeNotes'),
                'prototype' => null,
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            SingleText::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['parent', 'contentType'],
            $except
        );
    }
}
