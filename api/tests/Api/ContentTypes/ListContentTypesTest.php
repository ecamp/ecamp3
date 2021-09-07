<?php

namespace App\Tests\Api\ContentTypes;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListContentTypesTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListContentTypesIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/content_types');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 3,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('contentTypeSafetyConcept')],
            ['href' => $this->getIriFor('contentTypeNotes')],
            ['href' => $this->getIriFor('contentTypeColumnLayout')],
        ], $response->toArray()['_links']['items']);
    }
}
