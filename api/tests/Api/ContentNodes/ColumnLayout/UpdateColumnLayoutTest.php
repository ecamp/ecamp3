<?php

namespace App\Tests\Api\ColumnLayouts;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateColumnLayoutTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testPatchColumnLayoutIsAllowedForCollaborator() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('singleText1'),
            'slot' => '2',
            'position' => 1,
            'jsonConfig' => [],
            'instanceName' => 'Schlechtwetterprogramm',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'slot' => '2',
            'position' => 1,
            'jsonConfig' => [],
            'instanceName' => 'Schlechtwetterprogramm',
            '_links' => [
                'parent' => ['href' => $this->getIriFor('singleText1')],
            ],
        ]);
    }

    public function testPatchColumnLayoutValidatesParentBelongsToSameOwner() {
        $this->markTestSkipped('To be properly implemented. Currently returns 200 OK by mistake.');

        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('columnLayout2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must belong to the same owner.',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutValidatesNoParentLoop() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('singleText2'),
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
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'parent' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

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

    public function testPatchColumnLayoutDoesNotAllowParentOnRootColumnLayout() {
        $contentNode = static::$fixtures['columnLayout1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('columnLayout2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must be null on root content nodes.',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutDisallowsChangingContentType() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'contentType' => $this->getIriFor('contentTypeNotes'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("contentType" is unknown).',
        ]);
    }

    public function testPatchColumnLayoutAcceptsEmptySlot() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'slot' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'slot' => null,
        ]);
    }

    public function testPatchColumnLayoutAcceptsNullPosition() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'position' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'position' => null,
        ]);
    }

    public function testPatchColumnLayoutAcceptsNullJsonConfig() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'jsonConfig' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'jsonConfig' => null,
        ]);
    }

    public function testPatchColumnLayoutAcceptsNullInstanceName() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'instanceName' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'instanceName' => null,
        ]);
    }
}
