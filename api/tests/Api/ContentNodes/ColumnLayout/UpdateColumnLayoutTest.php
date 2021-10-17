<?php

namespace App\Tests\Api\ContentNodes\ColumnLayout;

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
            'columns' => [['slot' => '1', 'width' => 12]],
            'instanceName' => 'Schlechtwetterprogramm',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'slot' => '2',
            'position' => 1,
            'columns' => [['slot' => '1', 'width' => 12]],
            'instanceName' => 'Schlechtwetterprogramm',
            '_links' => [
                'parent' => ['href' => $this->getIriFor('singleText1')],
            ],
        ]);
    }

    public function testPatchColumnLayoutAcceptsValidJson() {
        $SINGLE_COLUMN_JSON_CONFIG = [
            ['slot' => '1', 'width' => 12],
        ];

        $contentNode = static::$fixtures['columnLayout2'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'columns' => $SINGLE_COLUMN_JSON_CONFIG,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'columns' => $SINGLE_COLUMN_JSON_CONFIG,
        ]);
    }

    public function testPatchColumnLayoutRejectsInvalidJson() {
        $INVALID_JSON_CONFIG = [
            'data' => 'value',
        ];

        $contentNode = static::$fixtures['columnLayout2'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'columns' => $INVALID_JSON_CONFIG,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'columns',
                    'message' => "Provided JSON doesn't match required schema.",
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutRejectsInvalidWidth() {
        $JSON_CONFIG = [
            ['slot' => '1', 'width' => 6],
            ['slot' => '2', 'width' => 5],
        ];

        $contentNode = static::$fixtures['columnLayout1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'columns' => $JSON_CONFIG,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'columns',
                    'message' => 'Expected column widths to sum to 12, but got a sum of 11',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutRejectsOrphanChildren() {
        $JSON_CONFIG = [
            ['slot' => '1', 'width' => 12],
        ];

        $contentNode = static::$fixtures['columnLayout1'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/column_layouts/'.$contentNode->getId(), ['json' => [
            'columns' => $JSON_CONFIG,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'columns',
                    'message' => 'The following slots still have child contents and should be present in the columns: 2',
                ],
            ],
        ]);
    }

    /**
     * From here: testing functionality of ContentNode base class.
     */
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
        $this->markTestSkipped('To be properly implemented. Currently returns a 200 OK by mistake.');

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
