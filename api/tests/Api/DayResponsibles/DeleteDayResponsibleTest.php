<?php

namespace App\Tests\Api\DayResponsibles;

use App\Entity\DayResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteDayResponsibleTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteDayResponsibleIsAllowedForCollaborator() {
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials()->request('DELETE', '/day_responsibles/'.$dayResponsible->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(DayResponsible::class)->find($dayResponsible->getId()));
    }
}
