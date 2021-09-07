<?php

namespace App\Tests\Api\Activities;

use App\Entity\Activity;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteActivityTest extends ECampApiTestCase {
    public function testDeleteActivityIsDeniedForAnonymousUser() {
        $activity = static::$fixtures['activity1'];
        static::createClient()->request('DELETE', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteActivityIsDeniedForUnrelatedUser() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('DELETE', '/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteActivityIsDeniedForGuest() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('DELETE', '/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteActivityIsAllowedForMember() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('DELETE', '/activities/'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Activity::class)->find($activity->getId()));
    }

    public function testDeleteActivityIsAllowedForManager() {
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials()->request('DELETE', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Activity::class)->find($activity->getId()));
    }

    public function testDeleteActivityFromPrototypeCampIsDeniedForUnrelatedUser() {
        $activity = static::$fixtures['activity1campPrototype'];
        static::createClientWithCredentials()->request('DELETE', '/activities/'.$activity->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
