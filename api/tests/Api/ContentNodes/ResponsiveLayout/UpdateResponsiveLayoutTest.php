<?php

namespace App\Tests\Api\ContentNodes\ResponsiveLayout;

use App\Tests\Api\ContentNodes\UpdateContentNodeTestCase;

/**
 * @internal
 */
class UpdateResponsiveLayoutTest extends UpdateContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/responsive_layouts';
        $this->defaultEntity = static::$fixtures['responsiveLayout1'];
    }

    public function testPatchResponsiveLayoutAcceptsValidJson() {
        $VALID_JSON_CONFIG = [
            ['slot' => 'main'],
            ['slot' => 'aside-top'],
            ['slot' => 'aside-bottom'],
        ];

        $contentNode = static::$fixtures['responsiveLayout1'];
        static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => ['data' => [
            'items' => $VALID_JSON_CONFIG,
        ]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['data' => [
            'items' => $VALID_JSON_CONFIG,
        ]]);
    }

    public function testPatchResponsiveLayoutRejectsInvalidJson() {
        $INVALID_JSON_CONFIG = [
            'data' => 'value',
        ];

        $contentNode = static::$fixtures['responsiveLayout1'];
        $response = static::createClientWithCredentials()->request('PATCH', $this->endpoint.'/'.$contentNode->getId(), ['json' => ['data' => [
            'items' => $INVALID_JSON_CONFIG,
        ]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonSchemaError($response, 'data');
    }
}
