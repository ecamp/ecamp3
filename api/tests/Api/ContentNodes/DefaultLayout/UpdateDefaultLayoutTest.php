<?php

namespace App\Tests\Api\ContentNodes\DefaultLayout;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateDefaultLayoutTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/default_layouts';
        $this->defaultEntity = static::$fixtures['defaultLayout1'];
    }

    public function testPatchDefaultLayoutAcceptsValidJson() {
        $VALID_JSON_CONFIG = [
            ['slot' => 'main'],
            ['slot' => 'aside-top'],
            ['slot' => 'aside-bottom'],
        ];

        $contentNode = static::$fixtures['defaultLayout1'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => ['data' => [
            'items' => $VALID_JSON_CONFIG,
        ]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => [
            'items' => $VALID_JSON_CONFIG,
        ]]);
    }

    public function testPatchDefaultLayoutRejectsInvalidJson() {
        $INVALID_JSON_CONFIG = [
            'data' => 'value',
        ];

        $contentNode = static::$fixtures['defaultLayout1'];
        $response = static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => ['data' => [
            'items' => $INVALID_JSON_CONFIG,
        ]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }
}
