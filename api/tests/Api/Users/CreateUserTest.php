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

    public function testLoginAfterRegistration() {
        $client = static::createBasicClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $client->request('POST', '/users', ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/authentication_token', ['json' => [
            'identifier' => 'bi-pi@example.com',
            'password' => 'learning-by-doing-101',
        ]]);

        $this->assertResponseIsSuccessful();
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

    public function testCreateUserCleansHTMLFromFirstname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'firstname' => 'Robert<script>alert(1)</script>',
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

    public function testCreateUserCleansHTMLFromSurname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'surname' => 'Baden-Powell<script>alert(1)</script>',
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

    public function testCreateUserCleansHTMLFromNickname() {
        static::createBasicClient()->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWritePayload(
                    mergeEmbeddedAttributes: [
                        'profile' => [
                            'nickname' => 'Bi-Pi<script>alert(1)</script>',
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
                    'message' => 'This value is too short. It should have 8 characters or more.',
                ],
            ],
        ]);
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
