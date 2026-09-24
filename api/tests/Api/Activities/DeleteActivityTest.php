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

    public function testDeleteActivityFromSharedCampIsDeniedForUnrelatedUser() {
        $activity = static::getFixture('activity1campShared');
        static::createClientWithCredentials()->request('DELETE', '/activities/'.$activity->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteActivityFromSharedCampIsDeniedForInactiveUser() {
        $activity = static::getFixture('activity1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])->request('DELETE', '/activities/'.$activity->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteActivityFromSharedCampIsDeniedForInvitedUser() {
        $activity = static::getFixture('activity1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])->request('DELETE', '/activities/'.$activity->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteActivityFromSharedCampIsAllowedForManager() {
        $activity = static::getFixture('activity1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])->request('DELETE', '/activities/'.$activity->getId());

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Activity::class)->find($activity->getId()));
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

    public function testDeleteActivityAlsoNullifiesCommentReferencesAndSetsOrphanDescription() {
        $client = static::createClientWithCredentials();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $activity = static::$fixtures['activity1'];
        $client->request('DELETE', $this->getIriFor($activity));
        $this->assertResponseStatusCodeSame(204);

        foreach (['comment1', 'comment2'] as $commentId) {
            $client->request('GET', $this->getIriFor(static::$fixtures[$commentId]));
            $this->assertResponseStatusCodeSame(200);
            $this->assertJsonContains([
                '_links' => [
                    'activity' => null,
                ],
                'orphanDescription' => $activity->category->short.' '.$activity->title,
            ]);
        }
    }
}
