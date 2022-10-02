<?php

namespace App\Tests\Api\Activities;

use App\Entity\Activity;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadActivityTest extends ECampApiTestCase {
    public function testGetSingleActivityIsDeniedForAnonymousUser() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1'];
        static::createBasicClient()->request('GET', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleActivityIsDeniedForUnrelatedUser() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/activities/'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleActivityIsDeniedForInactiveCollaborator() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/activities/'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleActivityIsAllowedForGuest() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('GET', '/activities/'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activity->getId(),
            'title' => $activity->title,
            'location' => $activity->location,
            '_links' => [
                'rootContentNode' => ['href' => $this->getIriFor('columnLayout1')],
                // 'contentNodes' => ['href' => '/content_nodes?owner=%2Factivities%2F'.$activity->getId()],
                'category' => ['href' => $this->getIriFor('category1')],
                'camp' => ['href' => $this->getIriFor('camp1')],
                'scheduleEntries' => ['href' => '/schedule_entries?activity=%2Factivities%2F'.$activity->getId()],
                'activityResponsibles' => ['href' => '/activity_responsibles?activity=%2Factivities%2F'.$activity->getId()],
            ],
        ]);
    }

    public function testGetSingleActivityIsAllowedForMember() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('GET', '/activities/'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activity->getId(),
            'title' => $activity->title,
            'location' => $activity->location,
            '_links' => [
                'rootContentNode' => ['href' => $this->getIriFor('columnLayout1')],
                // 'contentNodes' => ['href' => '/content_nodes?owner=%2Factivities%2F'.$activity->getId()],
                'category' => ['href' => $this->getIriFor('category1')],
                'camp' => ['href' => $this->getIriFor('camp1')],
                'scheduleEntries' => ['href' => '/schedule_entries?activity=%2Factivities%2F'.$activity->getId()],
                'activityResponsibles' => ['href' => '/activity_responsibles?activity=%2Factivities%2F'.$activity->getId()],
            ],
        ]);
    }

    public function testGetSingleActivityIsAllowedForManager() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials()->request('GET', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activity->getId(),
            'title' => $activity->title,
            'location' => $activity->location,
            '_links' => [
                'rootContentNode' => ['href' => $this->getIriFor('columnLayout1')],
                // 'contentNodes' => ['href' => '/content_nodes?owner=%2Factivities%2F'.$activity->getId()],
                'category' => ['href' => $this->getIriFor('category1')],
                'camp' => ['href' => $this->getIriFor('camp1')],
                'scheduleEntries' => ['href' => '/schedule_entries?activity=%2Factivities%2F'.$activity->getId()],
                'activityResponsibles' => ['href' => '/activity_responsibles?activity=%2Factivities%2F'.$activity->getId()],
            ],
        ]);
    }

    public function testGetSingleActivityFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1campPrototype'];
        static::createClientWithCredentials()->request('GET', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activity->getId(),
            'title' => $activity->title,
            'location' => $activity->location,
            '_links' => [
                'rootContentNode' => ['href' => $this->getIriFor('columnLayout1campPrototype')],
                'category' => ['href' => $this->getIriFor('category1campPrototype')],
                'camp' => ['href' => $this->getIriFor('campPrototype')],
            ],
        ]);
    }
}
