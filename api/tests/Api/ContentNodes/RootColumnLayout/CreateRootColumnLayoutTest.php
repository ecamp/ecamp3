<?php

namespace App\Tests\Api\ContentNodes\RootColumnLayout;

use App\Entity\ContentNode\ColumnLayout;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * Tests for creating a root column layout.
 *
 * @internal
 */
class CreateRootColumnLayoutTest extends CreateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/column_layouts';
        $this->entityClass = ColumnLayout::class;
        $this->defaultContentType = static::getFixture('contentTypeColumnLayout');
    }

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
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateColumnLayoutAllowsMissingPosition() {
        static::createClientWithCredentials()->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload([], ['position'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['position' => 2]);
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
                    'data' => [
                        'columns' => [['slot' => '1', 'width' => 12]],
                    ],
                ],
                $attributes
            ),
            $except
        );
    }
}
