<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCampCollaborationTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteCampCollaborationIsAllowedForCollaborator() {
        $campCollaboration = static::$fixtures['campCollaboration1'];
        static::createClientWithCredentials()->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(CampCollaboration::class)->find($campCollaboration->getId()));
    }
}
