<?php

namespace App\Tests\Api\ContentTypes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateContentTypeTest extends ECampApiTestCase {
    public function testPatchContentTypeIsNotAllowed() {
        $contentType = static::getFixture('contentTypeSafetyConcept');
        static::createClientWithCredentials()->request('PATCH', '/content_types/'.$contentType->getId(), ['json' => [
            'title' => 'Hello World',
            'location' => 'Stoos',
            'category' => $this->getIriFor('category2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(405); // method not allowed
    }
}
