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

    /*
    public function testCreateSingleTextSetsRootToParentsRoot() {
        static::createClientWithCredentials()->request('POST', '/content_node/single_texts', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'root' => ['href' => '/content_nodes/'.static::$fixtures['columnLayout1']->root->getId()],
        ]]);
    }

    public function testCreateSingleTextValidatesMissingParent() {
        static::createClientWithCredentials()->request('POST', '/content_node/single_texts', ['json' => $this->getExampleWritePayload([], ['parent'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must not be null on non-root content nodes.',
                ],
            ],
        ]);
    }

    public function testCreateSingleTextAllowsMissingSlot() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['slot'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['slot' => null]);
    }

    public function testCreateSingleTextAllowsMissingPosition() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['position'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['position' => null]);
    }

    public function testCreateSingleTextAllowsMissingJsonConfig() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['jsonConfig'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['jsonConfig' => null]);
    }

    public function testCreateSingleTextAllowsMissingInstanceName() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['instanceName'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['instanceName' => null]);
    }

    public function testCreateSingleTextValidatesMissingContentType() {
        static::createClientWithCredentials()->request('POST', '/content_nodes', ['json' => $this->getExampleWritePayload([], ['contentType'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'contentType',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }*/

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
