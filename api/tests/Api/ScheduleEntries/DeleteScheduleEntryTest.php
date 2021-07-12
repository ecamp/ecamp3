<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteScheduleEntryTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteScheduleEntryIsAllowedForCollaborator() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ScheduleEntry::class)->find($scheduleEntry->getId()));
    }
}
