<?php

namespace App\Tests\Api\ContentTypes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateContentTypeTest extends ECampApiTestCase {
    public function testCreateContentTypeIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/content_types', ['json' => [
            'name' => 'something',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(405); // method not allowed
    }
}
