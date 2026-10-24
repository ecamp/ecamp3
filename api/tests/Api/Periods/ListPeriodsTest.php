<?php

namespace App\Tests\Api\Periods;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListPeriodsTest extends ECampApiTestCase {
    public function testListPeriodsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/periods');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListPeriodsWithoutFilterIsNotAllowedForLoggedInUser() {
        // precondition: There is a period that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['period1campUnrelated']);

        static::createClientWithCredentials()->request('GET', '/periods');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListPeriodsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/periods?camp=%2Fcamps%2F'.$camp->getId());
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
            ['href' => $this->getIriFor('period1')],
            ['href' => $this->getIriFor('period2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/periods?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListPeriodsFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/periods?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListPeriodsFilteredByCampPrototypeIsAllowedForUnrelatedUser() {
        $camp = static::getFixture('campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/periods?camp=%2Fcamps%2F'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('period1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredBySharedCampIsAllowedForUnrelatedUser() {
        $camp = static::getFixture('campShared');
        $response = static::createClientWithCredentials()->request('GET', '/periods?camp=%2Fcamps%2F'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredBySharedCampIsAllowedForInactiveUser() {
        $camp = static::getFixture('campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/periods?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredBySharedCampIsAllowedForInvitedUser() {
        $camp = static::getFixture('campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('GET', '/periods?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('period1campShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredByCampCollaboratorIsAllowedForCollaborator() {
        $user = static::$fixtures['user1manager'];
        $response = static::createClientWithCredentials(['email' => $user->getEmail()])
            ->request('GET', '/periods?campCollaborator=/users/'.$user->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 3]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('period1')],
            ['href' => $this->getIriFor('period2')],
            ['href' => $this->getIriFor('period1camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListPeriodsFilteredByCampCollaboratorDoesNotRevealPeriodsOfUnrelatedUser() {
        $user = static::$fixtures['user1manager'];
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/periods?campCollaborator=/users/'.$user->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }
}
