<?php

namespace App\Tests\Api\ContentNodes\RootColumnLayout;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * Testing functionality of ContentNode with the root.
 *
 * @internal
 */
class UpdateRootColumnLayoutTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/column_layouts';
        $this->defaultEntity = static::getFixture('columnLayout1');
    }

    public function testPatchColumnLayoutValidatesParentBelongsToSameRoot() {
        $contentNode = static::getFixture('columnLayoutChild1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('columnLayout2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must belong to the same root.',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutValidatesNoParentLoop() {
        $contentNode = static::getFixture('columnLayout1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('columnLayoutChild1'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must not form a loop of parent-child relations.',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutValidatesMissingParent() {
        $contentNode = static::getFixture('columnLayoutChild1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'parent' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'This value should be of type App\Entity\ContentNode.',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutAllowsNullParentOnRootColumnLayout() {
        $contentNode = static::getFixture('columnLayout1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'parent' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testPatchColumnLayoutDoesNotAllowParentOnRootColumnLayout() {
        $contentNode = static::getFixture('columnLayout1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('columnLayout2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must belong to the same root.',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutDisallowsChangingContentType() {
        $contentNode = static::getFixture('columnLayoutChild1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'contentType' => $this->getIriFor('contentTypeNotes'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("contentType" is unknown).',
        ]);
    }

    public function testPatchColumnLayoutAcceptsEmptySlotForRoot() {
        $contentNode = static::getFixture('columnLayout1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'slot' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'slot' => null,
        ]);
    }

    public function testPatchColumnLayoutDisallowsNullPosition() {
        $contentNode = static::getFixture('columnLayoutChild1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'position' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "position" attribute must be "int", "NULL" given.',
        ]);
    }

    public function testPatchColumnLayoutAcceptsNullInstanceName() {
        $contentNode = static::getFixture('columnLayoutChild1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'instanceName' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'instanceName' => null,
        ]);
    }

    public function testPatchValidatesThatParentSupportsSlotName() {
        $this->defaultEntity = static::getFixture('columnLayoutChild1');
        $this->patch(payload: ['slot' => 'invalidSlot']);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                0 => [
                    'propertyPath' => 'slot',
                    'message' => 'This value should be one of [main,aside-top,aside-bottom], was invalidSlot.',
                ],
            ],
        ]);
    }
}
