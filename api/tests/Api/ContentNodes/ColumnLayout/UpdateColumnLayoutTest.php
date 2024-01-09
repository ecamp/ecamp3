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
        $this->defaultEntity = static::getFixture('columnLayoutChild1');
    }

    public function testPatchColumnLayoutAcceptsValidJson() {
        $VALID_JSON_CONFIG = [
            ['slot' => '1', 'width' => 5],
            ['slot' => '2', 'width' => 4],
            ['slot' => '3', 'width' => 3],
        ];

        $contentNode = static::getFixture('columnLayout2');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => ['data' => [
            'columns' => $VALID_JSON_CONFIG,
        ]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => [
            'columns' => $VALID_JSON_CONFIG,
        ]]);
    }

    public function testPatchColumnLayoutRejectsInvalidJson() {
        $INVALID_JSON_CONFIG = [
            'data' => 'value',
        ];

        $contentNode = static::getFixture('columnLayout2');
        $response = static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => ['data' => [
            'columns' => $INVALID_JSON_CONFIG,
        ]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }

    public function testPatchColumnLayoutRejectsInvalidWidth() {
        $JSON_CONFIG = [
            ['slot' => '1', 'width' => 6],
            ['slot' => '2', 'width' => 5],
        ];

        $contentNode = static::getFixture('columnLayout1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => ['data' => [
            'columns' => $JSON_CONFIG,
        ]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'data',
                    'message' => 'Expected column widths to sum to 12, but got a sum of 11',
                ],
            ],
        ]);
    }

    public function testPatchColumnLayoutRejectsOrphanChildren() {
        $JSON_CONFIG = [
            ['slot' => '1', 'width' => 12],
        ];

        $contentNode = static::getFixture('columnLayoutChild1');
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => ['data' => [
            'columns' => $JSON_CONFIG,
        ]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'data',
                    'message' => 'The following slots still have child contents and should be present in the columns: 2',
                ],
            ],
        ]);
    }
}
