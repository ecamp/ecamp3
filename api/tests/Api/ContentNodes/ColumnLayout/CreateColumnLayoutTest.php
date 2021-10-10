<?php

namespace App\Tests\Api\ColumnLayouts;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\ContentNode\ColumnLayout;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateColumnLayoutTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateColumnLayout() {
        static::createClientWithCredentials()->request('POST', '/content_node/column_layouts', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateColumnLayoutFromPrototype() {
        $prototype = static::$fixtures['columnLayout1'];
        static::createClientWithCredentials()->request('POST', '/content_node/column_layouts', ['json' => [
            'prototype' => $this->getIriFor('columnLayout1'),
            'parent' => $this->getIriFor('columnLayout1'),
            'contentType' => $this->getIriFor('contentTypeColumnLayout'),
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'columns' => $prototype->getColumns(),
            'instanceName' => $prototype->instanceName,
            'slot' => $prototype->slot,
            'position' => $prototype->position,
            'contentTypeName' => $prototype->getContentTypeName(),

            '_links' => [
                'contentType' => ['href' => $this->getIriFor($prototype->contentType)],
            ],
        ]);
    }

    public function testCreateColumnLayoutSetsRootToParentsRoot() {
        static::createClientWithCredentials()->request('POST', '/content_node/column_layouts', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'root' => ['href' => '/content_node/column_layouts/'.static::$fixtures['columnLayout1']->root->getId()],
        ]]);
    }

    public function testCreateColumnLayoutValidatesMissingParent() {
        $this->markTestSkipped('To be properly implemented. Currently throws a 500 error. This is caused by security_post_denormalize running before validation.');

        static::createClientWithCredentials()->request('POST', '/content_node/column_layouts', ['json' => $this->getExampleWritePayload([], ['parent'])]);

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

    public function testCreateColumnLayoutAllowsMissingSlot() {
        static::createClientWithCredentials()->request('POST', '/content_node/column_layouts', ['json' => $this->getExampleWritePayload([], ['slot'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['slot' => null]);
    }

    public function testCreateColumnLayoutAllowsMissingPosition() {
        static::createClientWithCredentials()->request('POST', '/content_node/column_layouts', ['json' => $this->getExampleWritePayload([], ['position'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['position' => null]);
    }

    public function testCreateColumnLayoutAllowsMissingInstanceName() {
        static::createClientWithCredentials()->request('POST', '/content_node/column_layouts', ['json' => $this->getExampleWritePayload([], ['instanceName'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['instanceName' => null]);
    }

    public function testCreateColumnLayoutValidatesMissingContentType() {
        static::createClientWithCredentials()->request('POST', '/content_node/column_layouts', ['json' => $this->getExampleWritePayload([], ['contentType'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'contentType',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ColumnLayout::class,
            OperationType::COLLECTION,
            'post',
            array_merge([
                'parent' => $this->getIriFor('columnLayout1'),
                'contentType' => $this->getIriFor('contentTypeColumnLayout'),
                'columns' => [['slot' => '1', 'width' => 12]],
                'prototype' => null,
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ColumnLayout::class,
            OperationType::ITEM,
            'get',
            array_merge([
                'columns' => [['slot' => '1', 'width' => 12]],
            ], $attributes),
            ['parent', 'contentType'],
            $except
        );
    }
}
