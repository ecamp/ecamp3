<?php

namespace App\Tests\Api\CampCollaborations;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListCampCollaborationsTest extends ECampApiTestCase {
    public function testListCampCollaborationsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/camp_collaborations');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListCampCollaborationsWithoutFilterIsNotAllowedForLoggedInUser() {
        // precondition: There is a camp collaboration that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['campCollaboration1campUnrelated']);

        static::createClientWithCredentials()->request('GET', '/camp_collaborations');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListCampCollaborationsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/camp_collaborations?camp=%2Fcamps%2F'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 6,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1manager')],
            ['href' => $this->getIriFor('campCollaboration2member')],
            ['href' => $this->getIriFor('campCollaboration3guest')],
            ['href' => $this->getIriFor('campCollaboration4invited')],
            ['href' => $this->getIriFor('campCollaboration5inactive')],
            ['href' => $this->getIriFor('campCollaboration6manager')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampCollaborationsAsCampSubResourceIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', "/camps/{$camp->getId()}/camp_collaborations");
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 6,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1manager')],
            ['href' => $this->getIriFor('campCollaboration2member')],
            ['href' => $this->getIriFor('campCollaboration3guest')],
            ['href' => $this->getIriFor('campCollaboration4invited')],
            ['href' => $this->getIriFor('campCollaboration5inactive')],
            ['href' => $this->getIriFor('campCollaboration6manager')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampCollaborationsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/camp_collaborations?camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListCampCollaborationsAsCampSubResourceIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', "/camps/{$camp->getId()}/camp_collaborations")
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testListCampCollaborationsFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/camp_collaborations?camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListCampCollaborationsAsCampSubResourceIsDeniedForInactiveCollaborator() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', "/camps/{$camp->getId()}/camp_collaborations")
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testListCampCollaborationsFilteredByCampPrototypeIsAllowedForUnrelatedUser() {
        $camp = static::getFixture('campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/camp_collaborations?camp=%2Fcamps%2F'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampCollaborationsFilteredByActivityResponsibleIsAllowedForCollaborator() {
        $activity = static::getFixture('activity1');
        $response = static::createClientWithCredentials()
            ->request('GET', '/camp_collaborations?activityResponsibles.activity=%2Factivities%2F'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1manager')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampCollaborationsFilteredByActivityResponsibleIsDeniedForUnrelatedUser() {
        $activity = static::getFixture('activity1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/camp_collaborations?activityResponsibles.activity=%2Factivities%2F'.$activity->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }
}
