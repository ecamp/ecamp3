<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadCampCollaborationTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleCampCollaborationIsAllowedForCollaborator() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials()->request('GET', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $campCollaboration->getId(),
            'role' => $campCollaboration->role,
            'status' => $campCollaboration->status,
            'inviteEmail' => null,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'user' => ['href' => $this->getIriFor('user1')],
            ],
        ]);
    }
}
