<?php

namespace App\Tests\Api\ActivityResponsibles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListActivityResponsiblesTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListActivityResponsiblesIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/activity_responsibles');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('activityResponsible1')],
        ], $response->toArray()['_links']['items']);
    }
}
