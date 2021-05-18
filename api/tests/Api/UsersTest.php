<?php

namespace App\Tests\Api;

class UsersTest extends ECampApiTestCase {
    public function testCreateUser() {
        static::createClient()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
        ]);
    }

    public function testListUsersIsDeniedToAnonymousUser() {
        static::createClient()->request('GET', '/users');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found'
        ]);
    }

    public function testListUsersIsAllowedForLoggedInUserButFiltered() {
        static::createClientWithCredentials()->request('GET', '/users');
        $this->assertResponseStatusCodeSame(200);
        $userIri = $this->getIriConverter()->getIriFromItem(static::$fixtures['user_1']);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_embedded' => [
                'item' => [
                    [
                        '_links' => [
                            'self' => [
                                'href' => $userIri
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testGetSingleUserIsDeniedToAnonymousUser() {
        $user = static::$fixtures['user_1'];
        static::createClient()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found'
        ]);
    }

    public function testGetSingleUserIsDeniedForDifferentUser() {
        $user2 = static::$fixtures['user_2'];
        static::createClientWithCredentials()->request('GET', '/users/'.$user2->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetSingleUserIsAllowedForSelf() {
        $user = static::$fixtures['user_1'];
        static::createClientWithCredentials()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            "email" => "test@example.com",
            "username" => "test-user",
            "firstname" => "Robert",
            "surname" => "Baden-Powell",
            "nickname" => "Bi-Pi",
            "language" => "en"
        ]);
    }
}
