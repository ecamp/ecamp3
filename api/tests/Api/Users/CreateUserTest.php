<?php

namespace App\Tests\Api\Users;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateUserTest extends ECampApiTestCase {
    public function testCreateUserWhenNotLoggedIn() {
        static::createBasicClient()->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([], ['password']));
    }

    public function testCreateUserWhenLoggedIn() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([], ['password']));
    }

    public function testLoginFailsWithoutActivation() {
        $client = static::createBasicClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $client->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/authentication_token', ['json' => [
            'identifier' => 'bi-pi@example.com',
            'password' => 'learning-by-doing-101',
        ]]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testLoginAfterRegistrationAndActivation() {
        $client = static::createBasicClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        // register user
        $result = $client->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(201);

        $userId = $result->toArray()['id'];
        $user = $this->getEntityManager()->getRepository(User::class)->find($userId);

        // activate user
        $client->request('PATCH', "/users/{$userId}/activate", ['json' => [
            'activationKey' => $user->activationKey,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseIsSuccessful();

        // login
        $client->request('POST', '/authentication_token', ['json' => [
            'identifier' => 'bi-pi@example.com',
            'password' => 'learning-by-doing-101',
        ]]);
        $this->assertResponseIsSuccessful();
    }

    public function testActivationFailsIfAlreadyActivated() {
        $client = static::createBasicClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        // register user
        $result = $client->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(201);

        $userId = $result->toArray()['id'];
        $user = $this->getEntityManager()->getRepository(User::class)->find($userId);

        // activate user
        $client->request('PATCH', "/users/{$userId}/activate", ['json' => [
            'activationKey' => $user->activationKey,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseIsSuccessful();

        // activate user again
        $client->request('PATCH', "/users/{$userId}/activate", ['json' => [
            'activationKey' => $user->activationKey,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testActivationFailsWithInvalidActivationKey() {
        $client = static::createBasicClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        // register user
        $result = $client->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(201);

        $userId = $result->toArray()['id'];

        // activate user
        $client->request('PATCH', "/users/{$userId}/activate", ['json' => [
            'activationKey' => '***',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateUserValidatesMissingProfile() {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload([], ['profile']),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesNullProfile() {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(['profile' => null], []),
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(
            [
                'title' => 'An error occurred',
                'detail' => 'Expected IRI or document for resource "App\\Entity\\Profile", "NULL" given.',
            ],
        );
    }

    public function testCreateUserDoesNotAllowToUseAnotherProfile() {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload([
                    'profile' => $this->getIriFor('profile1manager'),
                ]),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile',
                    'message' => 'Only one User can reference a Profile.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsEmail() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'email' => " bi-pi@example.com\t\t",
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                '_embedded' => [
                    'profile' => [
                        'email' => 'bi-pi@example.com',
                    ],
                ],
            ],
            ['password']
        ));
    }

    public function testCreateUserValidatesMissingEmail() {
        // use this easy way here, because unsetting a nested attribute would be complicated
        $exampleWritePayload = $this->getExampleWritePayload();
        unset($exampleWritePayload['profile']['email']);

        static::createClientWithCredentials()->request('POST', '/users', ['json' => $exampleWritePayload]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.email',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesBlankEmail() {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'email' => '',
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.email',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesLongEmail() {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'email' => 'test-with-a-very-long-email-address-which-is-not-really-realistic@example.com',
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.email',
                    'message' => 'This value is too long. It should have 64 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesInvalidEmail() {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'email' => 'test@sunrise',
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.email',
                    'message' => 'This value is not a valid email address.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesDuplicateEmail() {
        $client = static::createClientWithCredentials();
        $client->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'email' => static::$fixtures['user1manager']->getEmail(),
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.email',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsFirstThenValidatesDuplicateEmail() {
        $client = static::createClientWithCredentials();
        $client->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'email' => ' '.static::$fixtures['user1manager']->getEmail(),
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.email',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsFirstname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'firstname' => " Robert\t",
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                '_embedded' => [
                    'profile' => [
                        'firstname' => 'Robert',
                    ],
                ],
            ],
            ['password']
        ));
    }

    public function testCreateUserCleansForbiddenCharactersFromFirstname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'firstname' => "Robert\n\t",
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                '_embedded' => [
                    'profile' => [
                        'firstname' => 'Robert',
                    ],
                ],
            ],
            ['password']
        ));
    }

    public function testCreateUserValidatesFirstnameMaxLength() {
        $client = static::createClientWithCredentials();
        $client->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'firstname' => str_repeat('a', 65),
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.firstname',
                    'message' => 'This value is too long. It should have 64 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsSurname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'surname' => '   Baden-Powell',
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                '_embedded' => [
                    'profile' => [
                        'surname' => 'Baden-Powell',
                    ],
                ],
            ],
            ['password']
        ));
    }

    public function testCreateUserCleansForbiddenCharactersFromSurname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'surname' => "Baden-Powell\n\t",
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                '_embedded' => [
                    'profile' => [
                        'surname' => 'Baden-Powell',
                    ],
                ],
            ],
            ['password']
        ));
    }

    public function testCreateUserValidatesSurnameMaxLength() {
        $client = static::createClientWithCredentials();
        $client->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'surname' => str_repeat('a', 65),
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.surname',
                    'message' => 'This value is too long. It should have 64 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsNickname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'nickname' => "\tBi-Pi\t",
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                '_embedded' => [
                    'profile' => [
                        'nickname' => 'Bi-Pi',
                    ],
                ],
            ],
            ['password']
        ));
    }

    public function testCreateUserCleansForbiddenCharactersFromNickname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'nickname' => "Bi-Pi\n\t",
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                '_embedded' => [
                    'profile' => [
                        'nickname' => 'Bi-Pi',
                    ],
                ],
            ],
            ['password']
        ));
    }

    public function testCreateUserValidatesNicknameMaxLength() {
        $client = static::createClientWithCredentials();
        $client->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'nickname' => str_repeat('a', 33),
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.nickname',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateUserTrimsLanguage() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'language' => "\ten ",
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                '_embedded' => [
                    'profile' => [
                        'language' => 'en',
                    ],
                ],
            ],
            ['password']
        ));
    }

    public function testCreateUserValidatesInvalidLanguage() {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'language' => 'französisch',
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'profile.language',
                    'message' => 'The value you selected is not a valid choice.',
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
                    'message' => 'This value is too short. It should have 12 characters or more.',
                ],
            ],
        ]);
    }

    public function testCreateUserValidatesShortPassword() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'password' => 'only11chars',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value is too short. It should have 12 characters or more.',
                ],
            ],
        ]);
    }

    public function testCreateUserAllowsLongPassword() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'password' => 'this password has a total of 122 characters. this password has a total of 122 characters. OWASP approves of this password.',
        ])]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateUserValidatesUnreasonablyLongPassword() {
        static::createClientWithCredentials()->request('POST', '/users', ['json' => $this->getExampleWritePayload([
            'password' => 'this password has a total of more than 128 characters. this password has a total of more than 128 characters. OWASP does not approve this password.',
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    /**
     * @dataProvider notWriteableUserProperties
     */
    public function testNotWriteableUserProperties(string $property) {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        $property => 'something',
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => "Extra attributes are not allowed (\"{$property}\" is unknown).",
        ]);
    }

    public static function notWriteableUserProperties(): array {
        return [
            'activationKeyHash' => ['activationKeyHash'],
            'passwordResetKeyHash' => ['passwordResetKeyHash'],
        ];
    }

    /**
     * @dataProvider notWriteableProfileProperties
     */
    public function testNotWriteableProfileProperties(string $property) {
        static::createClientWithCredentials()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'profile' => [
                            $property => 'something',
                        ],
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => "Extra attributes are not allowed (\"{$property}\" is unknown).",
        ]);
    }

    public static function notWriteableProfileProperties(): array {
        return [
            'untrustedEmailKey' => ['untrustedEmailKey'],
            'untrustedEmailKeyHash' => ['untrustedEmailKeyHash'],
            'googleId' => ['googleId'],
            'pbsmidataId' => ['pbsmidataId'],
            'roles' => ['roles'],
            'user' => ['user'],
        ];
    }

    public function getExampleWritePayload($attributes = [], $except = [], $mergeEmbeddedAttributes = []) {
        $examplePayload = $this->getExamplePayload(
            User::class,
            OperationType::COLLECTION,
            'post',
            $attributes,
            [],
            $except
        );

        return array_replace_recursive($examplePayload, $mergeEmbeddedAttributes);
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        $exampleReadPayload = $this->getExamplePayload(
            User::class,
            OperationType::ITEM,
            'get',
            $attributes,
            [],
            $except
        );
        $exampleReadPayload['_embedded']['profile'] = $exampleReadPayload['profile'];
        unset($exampleReadPayload['profile']);

        return $exampleReadPayload;
    }
}
