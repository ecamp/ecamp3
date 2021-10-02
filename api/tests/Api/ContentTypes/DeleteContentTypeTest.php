<?php

namespace App\Tests\Api\ContentTypes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteContentTypeTest extends ECampApiTestCase {
    public function testDeleteContentTypeIsNotAllowed() {
        $contentType = static::$fixtures['contentTypeSafetyConcept'];
        static::createClientWithCredentials()->request('DELETE', '/content_types/'.$contentType->getId());

        $this->assertResponseStatusCodeSame(405); // method not allowed
    }
}
