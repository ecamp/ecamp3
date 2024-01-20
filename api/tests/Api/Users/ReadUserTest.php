<?php

namespace App\Tests\Api\Users;

use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadUserTest extends ECampApiTestCase {
    public function testGetSingleUserIsDeniedForAnonymousUser() {
        $user = static::getFixture('user1manager');
        static::createBasicClient()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetUnknownUserReturns404ForAnonymousUser() {
        static::createBasicClient()->request('GET', '/users/1');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetUnknownUserReturns404() {
        static::createClientWithCredentials()->request('GET', '/users/1');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetSingleUserIsAllowedForUnrelatedUser() {
        $user = static::getFixture('user4unrelated');
        static::createClientWithCredentials()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $user->getId(),
            'displayName' => $user->getDisplayName(),
            '_links' => [
                'self' => [
                    'href' => $this->getIriFor('user4unrelated'),
                ],
                'profile' => [
                    'href' => $this->getIriFor('profile4unrelated'),
                ],
            ],
        ]);
    }

    public function testGetSingleUserIsAllowedForSelf() {
        /** @var User $user */
        $user = static::getFixture('user1manager');
        static::createClientWithCredentials()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonEquals([
            'id' => $user->getId(),
            'displayName' => $user->getDisplayName(),
            '_links' => [
                'self' => [
                    'href' => $this->getIriFor('user1manager'),
                ],
                'profile' => [
                    'href' => $this->getIriFor('profile1manager'),
                ],
            ],
        ]);
    }

    public function testGetSingleUserIsAllowedForRelatedUser() {
        /** @var User $user */
        $user = static::getFixture('user2member');
        static::createClientWithCredentials()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonEquals([
            'id' => $user->getId(),
            'displayName' => $user->getDisplayName(),
            '_links' => [
                'self' => [
                    'href' => $this->getIriFor('user2member'),
                ],
                'profile' => [
                    'href' => $this->getIriFor('profile2member'),
                ],
            ],
        ]);
    }
}
