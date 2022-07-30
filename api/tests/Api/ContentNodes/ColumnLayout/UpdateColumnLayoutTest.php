<?php

namespace App\Tests\Api\ContentNodes\ColumnLayout;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateColumnLayoutTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/column_layouts';
        $this->defaultEntity = static::$fixtures['columnLayout1'];
    }

    public function testPatchColumnLayoutAcceptsValidJson() {
        $VALID_JSON_CONFIG = [
            ['slot' => '1', 'width' => 5],
            ['slot' => '2', 'width' => 4],
            ['slot' => '3', 'width' => 3],
        ];

        $contentNode = static::$fixtures['columnLayout2'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'columns' => $VALID_JSON_CONFIG,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'columns' => $VALID_JSON_CONFIG,
        ]);
    }

    public function testPatchColumnLayoutRejectsInvalidJson() {
        $INVALID_JSON_CONFIG = [
            'data' => 'value',
        ];

        $contentNode = static::$fixtures['columnLayout2'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
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
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
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
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
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
    public function testPatchColumnLayoutValidatesParentBelongsToSameRoot() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
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
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
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
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'parent' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'This value should be of type App\\Entity\\ContentNode.',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutDoesNotAllowParentOnRootColumnLayout() {
        $contentNode = static::$fixtures['columnLayout1'];
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
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'contentType' => $this->getIriFor('contentTypeNotes'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("contentType" is unknown).',
        ]);
    }

    public function testPatchColumnLayoutAcceptsEmptySlot() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'slot' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'slot' => null,
        ]);
    }

    public function testPatchColumnLayoutDisallowsNullPosition() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'position' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "position" attribute must be "int", "NULL" given.',
        ]);
    }

    public function testPatchColumnLayoutAcceptsNullInstanceName() {
        $contentNode = static::$fixtures['columnLayoutChild1'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => [
            'instanceName' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'instanceName' => null,
        ]);
    }
}
