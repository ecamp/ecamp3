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

    public function testCreateUserTrimsEmail() {
        static::createClient()->request('POST', '/users', ['json' => [
            'email' => " bi-pi@example.com\t\t",
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

    public function testCreateUserValidatesMissingEmail() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesBlankEmail() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => '',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesLongEmail() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'test-with-a-very-long-email-address-which-is-not-really-realistic@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is too long. It should have 64 characters or less.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesInvalidEmail() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'test@sunrise',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesDuplicateEmail() {
        $client = static::createClientWithCredentials();
        $client->request('POST', '/users', ['json' => [
            'email' => static::$fixtures['user_1']->getEmail(),
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is already used.'
                ]
            ]
        ]);
    }

    public function testCreateUserTrimsFirstThenValidatesDuplicateEmail() {
        $client = static::createClientWithCredentials();
        $client->request('POST', '/users', ['json' => [
            'email' => ' '.static::$fixtures['user_1']->getEmail(),
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is already used.'
                ]
            ]
        ]);
    }

    public function testCreateUserTrimsUsername() {
        static::createClient()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => '  bipi ',
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

    public function testCreateUserValidatesMissingUsername() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value should not be blank.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesBlankUsername() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => '',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value should not be blank.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesInvalidUsername() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'b*p',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is not valid.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesLongUsername() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'abcdefghijklmnopqrstuvwxyz-the-alphabet',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is too long. It should have 32 characters or less.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesDuplicateUsername() {
        $client = static::createClientWithCredentials();
        $client->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => static::$fixtures['user_1']->getUsername(),
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is already used.'
                ]
            ]
        ]);
    }

    public function testCreateUserTrimsFirstThenValidatesDuplicateUsername() {
        $client = static::createClientWithCredentials();
        $client->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => static::$fixtures['user_1']->getUsername().'   ',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is already used.'
                ]
            ]
        ]);
    }

    public function testCreateUserTrimsFirstname() {
        static::createClient()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => " Robert\t",
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

    public function testCreateUserTrimsSurname() {
        static::createClient()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => '   Baden-Powell',
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

    public function testCreateUserTrimsNickname() {
        static::createClient()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => "\tBi-Pi\t",
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

    public function testCreateUserTrimsLanguage() {
        static::createClient()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => "\ten ",
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

    public function testCreateUserValidatesInvalidLanguage() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'französisch',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'language',
                    'message' => 'This value is not a valid locale.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesLongLanguage() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'fr_CH.some-ridiculously-long-extension-which-is-technically-a-valid-ICU-locale',
            'password' => 'learning-by-doing-101'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'language',
                    'message' => 'This value is too long. It should have 20 characters or less.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesMissingPassword() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en'
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value should not be blank.'
                ]
            ]
        ]);
    }

    public function testCreateUserValidatesBlankPassword() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => [
            'email' => 'bi-pi@example.com',
            'username' => 'bipi',
            'firstname' => 'Robert',
            'surname' => 'Baden-Powell',
            'nickname' => 'Bi-Pi',
            'language' => 'en',
            'password' => ''
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value is too short. It should have 8 characters or more.'
                ]
            ]
        ]);
    }
}
