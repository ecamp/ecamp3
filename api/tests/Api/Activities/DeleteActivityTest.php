<?php

namespace App\Tests\Api\Activities;

use App\Entity\Activity;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteActivityTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteActivityIsAllowedForCollaborator() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials()->request('DELETE', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Activity::class)->find($activity->getId()));
    }
}
