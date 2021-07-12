<?php

namespace App\Tests\Api\Activities;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListActivitiesTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListActivitiesIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/activities');
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
            ['href' => $this->getIriFor('activity1')],
            ['href' => $this->getIriFor('activity2')],
            ['href' => $this->getIriFor('activity1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListActivitiesFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/activities?camp=/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 2,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('activity1')],
            ['href' => $this->getIriFor('activity2')],
        ], $response->toArray()['_links']['items']);
    }
}
