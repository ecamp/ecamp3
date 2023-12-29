<?php

namespace App\Tests\Api\Profiles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListProfilesTest extends ECampApiTestCase {
    public function testListProfilesIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/profiles');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testListProfilesIsAllowedForLoggedInUserButFiltered() {
        // precondition: There are multiple profiles that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['profile4unrelated']);
        $this->assertNotEmpty(static::$fixtures['profile5inactive']);
        $this->assertNotEmpty(static::$fixtures['profile6invited']);
        $this->assertNotEmpty(static::$fixtures['profileWithoutCampCollaborations']);
        $this->assertNotEmpty(static::$fixtures['profileWithStateDeleted']);

        $response = static::createClientWithCredentials()->request('GET', '/profiles');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 5,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profile1manager')],
            ['href' => $this->getIriFor('profile2member')],
            ['href' => $this->getIriFor('profile3guest')],
            ['href' => $this->getIriFor('profile7manager')],
            ['href' => $this->getIriFor('profile8memberOnlyInCamp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListProfilesForLoggedInUserWithoutCampCollaborationShowsOnlyThemself() {
        $profile = static::$fixtures['profileWithoutCampCollaborations'];
        $response = static::createClientWithCredentials(['email' => $profile->email])
            ->request('GET', '/profiles')
        ;
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
            ['href' => $this->getIriFor('profileWithoutCampCollaborations')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListProfilesFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 4,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profile1manager')],
            ['href' => $this->getIriFor('profile2member')],
            ['href' => $this->getIriFor('profile3guest')],
            ['href' => $this->getIriFor('profile7manager')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListProfilesFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListProfilesFilteredByCampForInactiveCollaboratorShowsOnlyThemself() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profile5inactive')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListProfilesFilteredByCampPrototypeIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }
}
