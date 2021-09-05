<?php

namespace App\Tests\Api\DayResponsibles;

use App\Entity\DayResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadDayResponsibleTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleDayResponsibleResponsibleIsAllowedForCollaborator() {
        /** @var DayResponsible $dayResponsible */
        $dayResponsible = static::$fixtures['dayResponsible1'];
        static::createClientWithCredentials()->request('GET', '/day_responsibles/'.$dayResponsible->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $dayResponsible->getId(),
            '_links' => [
                'day' => ['href' => $this->getIriFor('day1period1')],
                'campCollaboration' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
    }
}
