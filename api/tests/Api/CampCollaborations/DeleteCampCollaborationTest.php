<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCampCollaborationTest extends ECampApiTestCase {
    public function testDeleteCampCollaborationIsDeniedForAnonymousUser() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createBasicClient()->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteCampCollaborationIsDeniedForUnrelatedUser() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCampCollaborationIsDeniedForInactiveCollaborator() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCampCollaborationIsDeniedForGuest() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteCampCollaborationIsAllowedForMember() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(CampCollaboration::class)->find($campCollaboration->getId()));
    }

    public function testDeleteCampCollaborationIsAllowedForManager() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials()->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(CampCollaboration::class)->find($campCollaboration->getId()));
    }

    public function testDeleteCampCollaborationIsDeniedIfStatusNotInactive() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(422);
    }

    public function testDeleteCampCollaborationFromCampPrototypeIsDeniedForUnrelatedUser() {
        $campCollaboration = static::getFixture('campCollaboration1campPrototype');
        static::createClientWithCredentials()->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
