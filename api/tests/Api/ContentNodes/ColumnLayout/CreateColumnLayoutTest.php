<?php

namespace App\Tests\Api\ContentNodes\ColumnLayout;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\ContentNode\ColumnLayout;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateColumnLayoutTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/column_layouts';
        $this->entityClass = ColumnLayout::class;
        $this->defaultContentType = static::$fixtures['contentTypeColumnLayout'];
    }

    /**
     * tests specific for Columnlayout.
     */
    public function testCreateColumnLayout() {
        // when
        $this->create($this->getExampleWritePayload(['columns' => [['slot' => '1', 'width' => 5], ['slot' => '2', 'width' => 7]]]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['columns' => [['slot' => '1', 'width' => 5], ['slot' => '2', 'width' => 7]]]);
    }

    public function testCreateColumnLayoutFromPrototype() {
        $prototype = static::$fixtures['columnLayout1'];
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => [
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

    /**
     * tests common to all ContentNodes (tested only here).
     */
    public function testCreateColumnLayoutSetsRootToParentsRoot() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'root' => ['href' => $this->getIriFor(static::$fixtures['columnLayout1'])],
        ]]);
    }

    public function testCreateColumnLayoutValidatesMissingParent() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['parent'])]);

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
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['slot'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['slot' => null]);
    }

    public function testCreateColumnLayoutAllowsMissingPosition() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['position'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['position' => null]);
    }

    public function testCreateColumnLayoutAllowsMissingInstanceName() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['instanceName'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['instanceName' => null]);
    }

    public function testCreateColumnLayoutValidatesMissingContentType() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['contentType'])]);

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

    /**
     * payload set up.
     */
    public function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge(
                [
                    'columns' => [['slot' => '1', 'width' => 12]],
                ],
                $attributes
            ),
            $except
        );
    }

    /*
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
    }*/
}
