<?php

namespace App\Tests\Api\CampCollaborations;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListCampCollaborationsTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListCampCollaborationsIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/camp_collaborations');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 9,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1manager')],
            ['href' => $this->getIriFor('campCollaboration2member')],
            ['href' => $this->getIriFor('campCollaboration3guest')],
            ['href' => $this->getIriFor('campCollaboration1camp2')],
            ['href' => $this->getIriFor('campCollaboration4invited')],
            ['href' => $this->getIriFor('campCollaboration5inactive')],
            ['href' => $this->getIriFor('campCollaboration2invitedCampUnrelated')],
            ['href' => $this->getIriFor('campCollaboration1campUnrelated')],
            ['href' => $this->getIriFor('campCollaboration1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampCollaborationsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/camp_collaborations?camp=/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 5,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1manager')],
            ['href' => $this->getIriFor('campCollaboration2member')],
            ['href' => $this->getIriFor('campCollaboration3guest')],
            ['href' => $this->getIriFor('campCollaboration4invited')],
            ['href' => $this->getIriFor('campCollaboration5inactive')],
        ], $response->toArray()['_links']['items']);
    }
}
