<?php

namespace App\Tests\Api\Activities;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListActivitiesTest extends ECampApiTestCase {
    public function testListActivitiesIsDeniedForAnonymousUser() {
        static::createClient()->request('GET', '/activities');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListActivitiesIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is an activity that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['activity1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/activities');
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
            ['href' => $this->getIriFor('activity1')],
            ['href' => $this->getIriFor('activity2')],
            ['href' => $this->getIriFor('activity1camp2')],
            ['href' => $this->getIriFor('activity1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListActivitiesFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/activities?camp=/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 2,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('activity1')],
            ['href' => $this->getIriFor('activity2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListActivitiesFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/activities?camp=/camps/'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertStringNotContainsString($this->getIriFor('activity1'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('activity2'), $response->getContent());
    }

    public function testListActivitiesFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/activities?camp=/camps/'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertStringNotContainsString($this->getIriFor('activity1'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('activity2'), $response->getContent());
    }

    public function testListActivitiesFilteredByPrototypeCampIsAllowedForUnrelatedUser() {
        $camp = static::$fixtures['campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/activities?camp=/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('activity1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
