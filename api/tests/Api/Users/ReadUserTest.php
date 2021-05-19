<?php

namespace App\Tests\Api\Users;

use App\Tests\Api\ECampApiTestCase;

class ReadUserTest extends ECampApiTestCase {

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
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found'
        ]);
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

    public function testGetSingleUserIsAllowedForAdmin() {
        $user = static::$fixtures['user_1'];
        static::createClientWithAdminCredentials()->request('GET', '/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'email' => 'test@example.com',
            'username' => 'test-user',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en'
        ]);
    }
}
