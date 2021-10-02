<?php

namespace App\Tests\Api\ContentTypes;

use App\Entity\ContentType;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadContentTypeTest extends ECampApiTestCase {
    public function testGetSingleContentTypeIsAllowedForAnonymousUser() {
        /** @var ContentType $contentType */
        $contentType = static::$fixtures['contentTypeSafetyConcept'];
        static::createBasicClient()->request('GET', '/content_types/'.$contentType->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $contentType->getId(),
            'name' => $contentType->name,
            'active' => $contentType->active,
        ]);
    }

    public function testGetSingleContentTypeIsAllowedForLoggedInUser() {
        /** @var ContentType $contentType */
        $contentType = static::$fixtures['contentTypeSafetyConcept'];
        static::createClientWithCredentials()->request('GET', '/content_types/'.$contentType->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $contentType->getId(),
            'name' => $contentType->name,
            'active' => $contentType->active,
        ]);
    }
}
