<?php

namespace App\Tests\Api\ActivityResponsibles;

use App\Entity\ActivityResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadActivityResponsibleTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleActivityResponsibleResponsibleIsAllowedForCollaborator() {
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials()->request('GET', '/activity_responsibles/'.$activityResponsible->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activityResponsible->getId(),
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
    }
}
