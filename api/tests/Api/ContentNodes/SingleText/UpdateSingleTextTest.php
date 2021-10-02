<?php

namespace App\Tests\Api\SingleTexts;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateSingleTextTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testPatchSingleText() {
        $testText = 'TEST_TEXT';
        $contentNode = static::$fixtures['contentNodeChild2'];
        static::createClientWithCredentials()->request('PATCH', '/content_node/single_texts/'.$contentNode->getId(), ['json' => [
            'text' => $testText,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'text' => $testText,
        ]);
    }
}
