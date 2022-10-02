<?php

namespace App\Tests\Api\Profiles;

use App\Entity\Profile;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadProfileTest extends ECampApiTestCase {
    public function testGetSingleProfileIsDeniedForAnonymousUser() {
        $user = static::$fixtures['user1manager'];
        static::createBasicClient()->request('GET', '/profiles/'.$user->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetSingleProfileIsDeniedForUnrelatedUser() {
        $user2 = static::$fixtures['user4unrelated'];
        static::createClientWithCredentials()->request('GET', '/profiles/'.$user2->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetSingleProfileIsAllowedForSelf() {
        /** @var Profile $profile */
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('GET', '/profiles/'.$profile->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $profile->getId(),
            'email' => $profile->email,
            'firstname' => $profile->firstname,
            'surname' => $profile->surname,
            'nickname' => $profile->nickname,
            'language' => $profile->language,
            '_links' => [
                'self' => [
                    'href' => '/profiles/'.$profile->getId(),
                ],
                'user' => [
                    'href' => $this->getIriFor('user1manager'),
                ],
            ],
        ]);
    }

    public function testGetSingleProfileIsAllowedForRelatedUser() {
        /** @var Profile $profile */
        $profile = static::$fixtures['profile2member'];
        static::createClientWithCredentials()->request('GET', '/profiles/'.$profile->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $profile->getId(),
            'email' => $profile->email,
            'firstname' => $profile->firstname,
            'surname' => $profile->surname,
            'nickname' => $profile->nickname,
            'language' => $profile->language,
            '_links' => [
                'self' => [
                    'href' => '/profiles/'.$profile->getId(),
                ],
                'user' => [
                    'href' => $this->getIriFor('user2member'),
                ],
            ],
        ]);
    }

    public function testGetSingleProfileIsAllowedForSelfIfSelfHasNoCampCollaborations() {
        /** @var Profile $profile */
        $profile = static::$fixtures['profileWithoutCampCollaborations'];
        static::createClientWithCredentials(['email' => $profile->email])
            ->request('GET', '/profiles/'.$profile->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $profile->getId(),
            'email' => $profile->email,
            'firstname' => $profile->firstname,
            'surname' => $profile->surname,
            'nickname' => $profile->nickname,
            'language' => $profile->language,
            '_links' => [
                'self' => [
                    'href' => '/profiles/'.$profile->getId(),
                ],
                'user' => [
                    'href' => $this->getIriFor('userWithoutCampCollaborations'),
                ],
            ],
        ]);
    }
}
