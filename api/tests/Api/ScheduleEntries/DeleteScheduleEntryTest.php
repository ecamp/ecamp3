<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteScheduleEntryTest extends ECampApiTestCase {
    public function testDeleteScheduleEntryIsDeniedForAnonymousUser() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createBasicClient()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteScheduleEntryIsDeniedForUnrelatedUser() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteScheduleEntryIsDeniedForInactiveCollaborator() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteScheduleEntryIsDeniedForGuest() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteScheduleEntryIsAllowedForMember() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ScheduleEntry::class)->find($scheduleEntry->getId()));
    }

    public function testDeleteScheduleEntryIsAllowedForManager() {
        $scheduleEntry = static::$fixtures['scheduleEntry1'];
        static::createClientWithCredentials()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ScheduleEntry::class)->find($scheduleEntry->getId()));
    }

    public function testDeleteScheduleEntryFromCampPrototypeIsDeniedForUnrelatedUser() {
        $scheduleEntry = static::$fixtures['scheduleEntry1period1campPrototype'];
        static::createClientWithCredentials()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteScheduleEntryIsDeniedForLastScheduleEntryOfActivity() {
        $scheduleEntry = static::$fixtures['scheduleEntry1period1camp1'];
        static::createClientWithCredentials()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'activity.scheduleEntries: An activity must have at least one ScheduleEntry',
            'violations' => [
                0 => [
                    'propertyPath' => 'activity.scheduleEntries',
                    'message' => 'An activity must have at least one ScheduleEntry',
                ],
            ],
        ]);
    }
}
