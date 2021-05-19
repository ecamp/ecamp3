<?php

namespace App\Tests\Api\Users;

use App\Tests\Api\ECampApiTestCase;

class CreateUserTest extends ECampApiTestCase {

    public function testCreateUserWhenNotLoggedIn() {
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

    public function testCreateUserWhenLoggedIn() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
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

    public function testLoginAfterRegistration() {
        $client = static::createClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $client->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/authentication_token', ['json' => [
            'username' => 'bipi',
            'password' => 'learning-by-doing-101',
        ]]);

        $this->assertResponseIsSuccessful();
    }
}
