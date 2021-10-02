<?php

namespace App\Tests\Api\ContentNodes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateContentNodeTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function setUp(): void {
        $this->markTestSkipped('Tests temporarily inactive (rewritings tests TBD)');
    }

    public function testPatchContentNodeIsAllowedForCollaborator() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('contentNodeChild2'),
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
                'parent' => ['href' => $this->getIriFor('contentNodeChild2')],
            ],
        ]);
    }

    public function testPatchContentNodeValidatesParentBelongsToSameOwner() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('contentNode2'),
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

    public function testPatchContentNodeValidatesNoParentLoop() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('contentNodeGrandchild1'),
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

    public function testPatchContentNodeValidatesMissingParent() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
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

    public function testPatchContentNodeDoesNotAllowParentOnRootContentNode() {
        $contentNode = static::$fixtures['contentNode1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'parent' => $this->getIriFor('contentNode2'),
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

    public function testPatchContentNodeDisallowsChangingContentType() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'contentType' => $this->getIriFor('contentTypeNotes'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("contentType" is unknown).',
        ]);
    }

    public function testPatchContentNodeAcceptsEmptySlot() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'slot' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'slot' => null,
        ]);
    }

    public function testPatchContentNodeAcceptsNullPosition() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'position' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'position' => null,
        ]);
    }

    public function testPatchContentNodeAcceptsNullJsonConfig() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'jsonConfig' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'jsonConfig' => null,
        ]);
    }

    public function testPatchContentNodeAcceptsNullInstanceName() {
        $contentNode = static::$fixtures['contentNodeChild1'];
        static::createClientWithCredentials()->request('PATCH', '/content_nodes/'.$contentNode->getId(), ['json' => [
            'instanceName' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'instanceName' => null,
        ]);
    }
}
