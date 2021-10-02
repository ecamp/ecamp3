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
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('GET', '/activities/'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activity->getId(),
            'title' => $activity->title,
            'location' => $activity->location,
            '_links' => [
                'rootContentNode' => ['href' => $this->getIriFor('columnLayout1')],
                //'contentNodes' => ['href' => '/content_nodes?owner=/activities/'.$activity->getId()],
                'category' => ['href' => $this->getIriFor('category1')],
                'camp' => ['href' => $this->getIriFor('camp1')],
                'scheduleEntries' => ['href' => '/schedule_entries?activity=/activities/'.$activity->getId()],
                //'campCollaborations' => ['href' => '/camp_collaborations?activity=/activities/'.$activity->getId()],
            ],
        ]);
    }

    public function testGetSingleActivityIsAllowedForMember() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('GET', '/activities/'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activity->getId(),
            'title' => $activity->title,
            'location' => $activity->location,
            '_links' => [
                'rootContentNode' => ['href' => $this->getIriFor('columnLayout1')],
                //'contentNodes' => ['href' => '/content_nodes?owner=/activities/'.$activity->getId()],
                'category' => ['href' => $this->getIriFor('category1')],
                'camp' => ['href' => $this->getIriFor('camp1')],
                'scheduleEntries' => ['href' => '/schedule_entries?activity=/activities/'.$activity->getId()],
                //'campCollaborations' => ['href' => '/camp_collaborations?activity=/activities/'.$activity->getId()],
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
                //'contentNodes' => ['href' => '/content_nodes?owner=/activities/'.$activity->getId()],
                'category' => ['href' => $this->getIriFor('category1')],
                'camp' => ['href' => $this->getIriFor('camp1')],
                'scheduleEntries' => ['href' => '/schedule_entries?activity=/activities/'.$activity->getId()],
                //'campCollaborations' => ['href' => '/camp_collaborations?activity=/activities/'.$activity->getId()],
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
