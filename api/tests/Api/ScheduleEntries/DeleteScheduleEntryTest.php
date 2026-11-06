<?php

namespace App\Tests\Api\ScheduleEntries;

use App\Entity\ScheduleEntry;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteScheduleEntryTest extends ECampApiTestCase {
    public function testDeleteScheduleEntryIsDeniedForAnonymousUser() {
        $scheduleEntry = static::getFixture('scheduleEntry1');
        static::createBasicClient()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteScheduleEntryIsDeniedForUnrelatedUser() {
        $scheduleEntry = static::getFixture('scheduleEntry1');
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
        $scheduleEntry = static::getFixture('scheduleEntry1');
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
        $scheduleEntry = static::getFixture('scheduleEntry1');
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
        $scheduleEntry = static::getFixture('scheduleEntry1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ScheduleEntry::class)->find($scheduleEntry->getId()));
    }

    public function testDeleteScheduleEntryIsAllowedForManager() {
        $scheduleEntry = static::getFixture('scheduleEntry1');
        static::createClientWithCredentials()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ScheduleEntry::class)->find($scheduleEntry->getId()));
    }

    public function testDeleteScheduleEntryFromCampPrototypeIsDeniedForUnrelatedUser() {
        $scheduleEntry = static::getFixture('scheduleEntry1period1campPrototype');
        static::createClientWithCredentials()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteScheduleEntryFromSharedCampIsDeniedForUnrelatedUser() {
        $scheduleEntry = static::getFixture('scheduleEntry1period1campShared');
        static::createClientWithCredentials()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteScheduleEntryFromSharedCampIsDeniedForInactiveUser() {
        $scheduleEntry = static::getFixture('scheduleEntry1period1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteScheduleEntryFromSharedCampIsDeniedForInvitedUser() {
        $scheduleEntry = static::getFixture('scheduleEntry1period1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteScheduleEntryIsDeniedForLastScheduleEntryOfActivity() {
        $scheduleEntry = static::getFixture('scheduleEntry1period1camp1');
        static::createClientWithCredentials()->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'activity.scheduleEntries: Cannot delete the last schedule entry.',
            'violations' => [
                0 => [
                    'propertyPath' => 'activity.scheduleEntries',
                    'message' => 'Cannot delete the last schedule entry.',
                ],
            ],
        ]);
    }

    public function testDeleteScheduleEntryPurgesAllScheduleEntryNumbersInThePeriod() {
        $client = static::createClientWithCredentials();
        $cacheManager = $this->mockCacheManager();

        $scheduleEntry = static::getFixture('scheduleEntry3');
        $client->request('DELETE', '/schedule_entries/'.$scheduleEntry->getId());

        $this->assertResponseStatusCodeSame(204);
        self::assertContains(static::getFixture('period2')->getId().'^scheduleEntryNumbers', $cacheManager->getInvalidatedTags());
    }
}
