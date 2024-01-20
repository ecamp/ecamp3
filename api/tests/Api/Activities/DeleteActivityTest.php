<?php

namespace App\Tests\Api\Activities;

use App\Entity\Activity;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteActivityTest extends ECampApiTestCase {
    public function testDeleteActivityIsDeniedForAnonymousUser() {
        $activity = static::getFixture('activity1');
        static::createBasicClient()->request('DELETE', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteActivityIsDeniedForUnrelatedUser() {
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteActivityIsDeniedForInactiveCollaborator() {
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteActivityIsDeniedForGuest() {
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteActivityIsAllowedForMember() {
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/activities/'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Activity::class)->find($activity->getId()));
    }

    public function testDeleteActivityIsAllowedForManager() {
        $activity = static::getFixture('activity1');
        static::createClientWithCredentials()->request('DELETE', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Activity::class)->find($activity->getId()));
    }

    public function testDeleteActivityFromCampPrototypeIsDeniedForUnrelatedUser() {
        $activity = static::getFixture('activity1campPrototype');
        static::createClientWithCredentials()->request('DELETE', '/activities/'.$activity->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteActivityAlsoDeletesContentNodes() {
        $client = static::createClientWithCredentials();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $client->request('DELETE', $this->getIriFor(static::$fixtures['activity1']));
        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', $this->getIriFor(static::$fixtures['columnLayout1']));
        $this->assertResponseStatusCodeSame(404);

        $client->request('GET', $this->getIriFor(static::$fixtures['multiSelect1']));
        $this->assertResponseStatusCodeSame(404);
    }
}
