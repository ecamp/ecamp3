<?php

namespace App\Tests\Api\Camps;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListCampsTest extends ECampApiTestCase {
    public function testListCampsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/camps');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListCampsWithoutFilterIsNotAllowedForLoggedInUser() {
        static::createClientWithCredentials()->request('GET', '/camps');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListCampsFilteredByIsPrototypeFalseIsNotAllowed() {
        static::createClientWithCredentials()->request('GET', '/camps?isPrototype=false');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListCampsWithIgnoredFilterValueIsNotAllowed() {
        static::createClientWithCredentials()->request('GET', '/camps?campCollaborator[foo]=bar');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListPrototypeCampsOnly() {
        $response = static::createClientWithCredentials()->request('GET', '/camps?isPrototype=true');
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
            ['href' => $this->getIriFor('campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampsFilteredByCurrentUser() {
        $user = static::$fixtures['user1manager'];
        $response = static::createClientWithCredentials(['email' => $user->getEmail()])->request('GET', '/camps?campCollaborator=/users/'.$user->getId());
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
            ['href' => $this->getIriFor('camp1')],
            ['href' => $this->getIriFor('camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampsFilteredByOtherCollaboratorIsAllowedButFiltered() {
        $user = static::$fixtures['user1manager'];
        $user2 = static::$fixtures['user8memberOnlyInCamp2'];
        $response = static::createClientWithCredentials(['email' => $user2->getEmail()])->request('GET', '/camps?campCollaborator=/users/'.$user->getId());
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
            ['href' => $this->getIriFor('camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampsFilteredByUnrelatedUserIsAllowedButFiltered() {
        $user = static::$fixtures['user1manager'];
        $user2 = static::$fixtures['user4unrelated'];
        static::createClientWithCredentials(['email' => $user2->getEmail()])->request('GET', '/camps?campCollaborator=/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 0,
            '_links' => [],
            '_embedded' => [
                'items' => [],
            ],
        ]);
    }

    public function testListCampsDoesNotShowCampToInactiveCollaborator() {
        $user = static::$fixtures['user5inactive'];
        $response = static::createClientWithCredentials(['email' => $user->getEmail()])
            ->request('GET', '/camps?campCollaborator=/users/'.$user->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 0,
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }
}
