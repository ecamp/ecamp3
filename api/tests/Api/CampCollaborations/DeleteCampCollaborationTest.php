<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCampCollaborationTest extends ECampApiTestCase {
    public function testDeleteCampCollaborationIsDeniedForAnonymousUser() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClient()->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteCampCollaborationIsDeniedForUnrelatedUser() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCampCollaborationIsDeniedForInactiveCollaborator() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCampCollaborationIsDeniedForGuest() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteCampCollaborationIsAllowedForMember() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(CampCollaboration::class)->find($campCollaboration->getId()));
    }

    public function testDeleteCampCollaborationIsAllowedForManager() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials()->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(CampCollaboration::class)->find($campCollaboration->getId()));
    }

    public function testDeleteCampCollaborationFromCampPrototypeIsDeniedForUnrelatedUser() {
        $campCollaboration = static::$fixtures['campCollaboration1campPrototype'];
        static::createClientWithCredentials()->request('DELETE', '/camp_collaborations/'.$campCollaboration->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
