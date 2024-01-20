<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadCampCollaborationTest extends ECampApiTestCase {
    public function testGetSingleCampCollaborationIsDeniedForAnonymousUser() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createBasicClient()->request('GET', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleCampCollaborationIsDeniedForUnrelatedUser() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/camp_collaborations/'.$campCollaboration->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCampCollaborationIsDeniedForInactiveCollaborator() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/camp_collaborations/'.$campCollaboration->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCampCollaborationIsAllowedForGuest() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('GET', '/camp_collaborations/'.$campCollaboration->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $campCollaboration->getId(),
            'role' => $campCollaboration->role,
            'status' => $campCollaboration->status,
            'inviteEmail' => null,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'user' => ['href' => $this->getIriFor('user1manager')],
            ],
        ]);
    }

    public function testGetSingleCampCollaborationIsAllowedForMember() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('GET', '/camp_collaborations/'.$campCollaboration->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $campCollaboration->getId(),
            'role' => $campCollaboration->role,
            'status' => $campCollaboration->status,
            'inviteEmail' => null,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'user' => ['href' => $this->getIriFor('user1manager')],
            ],
        ]);
    }

    public function testGetSingleCampCollaborationIsAllowedForManager() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('GET', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $campCollaboration->getId(),
            'role' => $campCollaboration->role,
            'status' => $campCollaboration->status,
            'inviteEmail' => null,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'user' => ['href' => $this->getIriFor('user1manager')],
            ],
        ]);
    }

    public function testGetSingleCampCollaborationFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration1campPrototype');
        static::createClientWithCredentials()->request('GET', '/camp_collaborations/'.$campCollaboration->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $campCollaboration->getId(),
            'role' => $campCollaboration->role,
            'status' => $campCollaboration->status,
            'inviteEmail' => null,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('campPrototype')],
                'user' => ['href' => $this->getIriFor('admin')],
            ],
        ]);
    }

    public function testSqlQueryCount() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration1manager');

        $client = static::createClientWithCredentials();
        $client->enableProfiler();
        $client->request('GET', '/camp_collaborations/'.$campCollaboration->getId());

        $this->assertSqlQueryCount($client, 15);
    }
}
