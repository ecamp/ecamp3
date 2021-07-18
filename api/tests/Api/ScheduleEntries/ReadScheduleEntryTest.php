<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadScheduleEntryTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleScheduleEntryIsAllowedForCollaborator() {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('GET', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $scheduleEntry->getId(),
            'periodOffset' => $scheduleEntry->periodOffset,
            'length' => $scheduleEntry->length,
            'left' => 0,
            'width' => 1,
            'dayNumber' => 1,
            'scheduleEntryNumber' => 1,
            'number' => '1.1',
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
                'period' => ['href' => $this->getIriFor('period1')],
                'day' => ['href' => $this->getIriFor('day1period1')],
            ],
        ]);
    }
}
