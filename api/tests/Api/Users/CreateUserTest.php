<?php

namespace App\Tests\Api\Users;

use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateUserTest extends ECampApiTestCase {
    public function testCreateUserWhenNotLoggedIn() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([], ['password']));
    }

    public function testCreateUserWhenLoggedIn() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([], ['password']));
    }

    public function testLoginAfterRegistration() {
        $client = static::createClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $client->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/authentication_token', ['json' => [
            'username' => 'bipi',
            'password' => 'learning-by-doing-101',
        ]]);

        $this->assertResponseIsSuccessful();
    }

    public function testCreateUserTrimsEmail() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'email' => " bi-pi@example.com\t\t",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'email' => 'bi-pi@example.com',
        ], ['password']));
    }

    public function testCreateUserValidatesMissingEmail() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([], ['email'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesBlankEmail() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'email' => '',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesLongEmail() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'email' => 'test-with-a-very-long-email-address-which-is-not-really-realistic@example.com',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is too long. It should have 64 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesInvalidEmail() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'email' => 'test@sunrise',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesDuplicateEmail() {
        $client = static::createClientWithCredentials();
        $client->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'email' => static::$fixtures['user1']->email,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsFirstThenValidatesDuplicateEmail() {
        $client = static::createClientWithCredentials();
        $client->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'email' => ' '.static::$fixtures['user1']->email,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsUsername() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'username' => '  bipi ',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'username' => 'bipi',
        ], ['password']));
    }

    public function testCreateUserValidatesMissingUsername() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([], ['username'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesBlankUsername() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'username' => '',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesInvalidUsername() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'username' => 'b*p',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is not valid.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesLongUsername() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'username' => 'abcdefghijklmnopqrstuvwxyz-the-alphabet',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesDuplicateUsername() {
        $client = static::createClientWithCredentials();
        $client->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'username' => static::$fixtures['user1']->username,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsFirstThenValidatesDuplicateUsername() {
        $client = static::createClientWithCredentials();
        $client->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'username' => static::$fixtures['user1']->username.'   ',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsFirstname() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'firstname' => " Robert\t",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'firstname' => 'Robert',
        ], ['password']));
    }

    public function testCreateUserCleansHTMLFromFirstname() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'firstname' => 'Robert<script>alert(1)</script>',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'firstname' => 'Robert',
        ], ['password']));
    }

    public function testCreateUserTrimsSurname() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'surname' => '   Baden-Powell',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'surname' => 'Baden-Powell',
        ], ['password']));
    }

    public function testCreateUserCleansHTMLFromSurname() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'surname' => 'Baden-Powell<script>alert(1)</script>',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'surname' => 'Baden-Powell',
        ], ['password']));
    }

    public function testCreateUserTrimsNickname() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'nickname' => "\tBi-Pi\t",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'nickname' => 'Bi-Pi',
        ], ['password']));
    }

    public function testCreateUserCleansHTMLFromNickname() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'nickname' => 'Bi-Pi<script>alert(1)</script>',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'nickname' => 'Bi-Pi',
        ], ['password']));
    }

    public function testCreateUserTrimsLanguage() {
        static::createClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'language' => "\ten ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'language' => 'en',
        ], ['password']));
    }

    public function testCreateUserValidatesInvalidLanguage() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'language' => 'französisch',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'language',
                    'message' => 'This value is not a valid locale.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesLongLanguage() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'language' => 'fr_CH.some-ridiculously-long-extension-which-is-technically-a-valid-ICU-locale',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'language',
                    'message' => 'This value is too long. It should have 20 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesMissingPassword() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([], ['password'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesBlankPassword() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'password' => '',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value is too short. It should have 8 characters or more.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(User::class, $attributes, [], $except);
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(User::class, $attributes, [], $except);
    }
}
