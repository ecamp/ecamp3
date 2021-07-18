<?php

namespace App\Tests\Api\ActivityResponsibles;

use App\Entity\ActivityResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteActivityResponsibleTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteActivityResponsibleIsAllowedForCollaborator() {
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials()->request('DELETE', '/activity_responsibles/'.$activityResponsible->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ActivityResponsible::class)->find($activityResponsible->getId()));
    }
}
