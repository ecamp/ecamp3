<?php

namespace App\Tests\Api\Periods;

use App\Entity\Period;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeletePeriodTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeletePeriodIsAllowedForCollaborator() {
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('DELETE', '/periods/'.$period->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Period::class)->find($period->getId()));
    }
}
