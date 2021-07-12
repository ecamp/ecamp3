<?php

namespace App\Tests\Api\Days;

use App\Entity\Day;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadDayTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleDayIsAllowedForCollaborator() {
        /** @var Day $day */
        $day = static::$fixtures['day1period1'];
        static::createClientWithCredentials()->request('GET', '/days/'.$day->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $day->getId(),
            'dayOffset' => $day->dayOffset,
            'number' => $day->getDayNumber(),
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                //'scheduleEntries' => ['href' => '/schedule_entries?day=/days/'.$day->getId()],
                'dayResponsibles' => ['href' => '/day_responsibles?day=/days/'.$day->getId()],
            ],
        ]);
    }
}
