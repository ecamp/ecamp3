<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCampCollaborationTest extends ECampApiTestCase {
    public function testCreateCampCollaborationIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'user' => $this->getIriFor('user4unrelated'),
        ])]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateCampCollaborationIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
                'user' => $this->getIriFor('user4unrelated'),
            ])])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateCampCollaborationIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
                'user' => $this->getIriFor('user5inactive'),
            ])])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateCampCollaborationIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
                'user' => $this->getIriFor('user3guest'),
            ])])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateCampCollaborationIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/camp_collaborations',
                [
                    'json' => $this->getExampleWritePayload(
                        [
                            'inviteEmail' => 'someone@example.com',
                        ],
                        ['user']
                    ),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'inviteEmail' => 'someone@example.com',
            '_links' => [
                'user' => null,
            ],
        ]));
    }

    public function testCreateCampCollaborationIsAllowedForManager() {
        static::createClientWithCredentials()->request(
            'POST',
            '/camp_collaborations',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'inviteEmail' => 'someone@example.com',
                    ],
                    ['user']
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'inviteEmail' => 'someone@example.com',
            '_links' => [
                'user' => null,
            ],
        ]));
    }

    public function testCreateCampCollaborationWithUser() {
        static::createClientWithCredentials()->request(
            'POST',
            '/camp_collaborations',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'user' => $this->getIriFor('user4unrelated'),
                    ],
                    ['inviteEmail']
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            '_links' => [
                'user' => ['href' => $this->getIriFor('user4unrelated')],
            ],
        ]));
    }

    public function testCreateCampCollaborationWithUserCreatesMaterialList() {
        $client = static::createClientWithCredentials();
        $client->disableReboot();

        $client->request('GET', '/material_lists?camp='.$this->getIriFor('camp1'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 3,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);

        $client->request(
            'POST',
            '/camp_collaborations',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'user' => $this->getIriFor('user4unrelated'),
                    ],
                    ['inviteEmail']
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);

        $client->request('GET', '/material_lists?camp='.$this->getIriFor('camp1'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 4,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
    }

    public function testCreateCampCollaborationInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateCampCollaborationValidatesIfInviteEmailIsBlank() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => '',
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'inviteEmail: This value is too short. It should have 1 character or more.',
        ]);
    }

    public function testCreateCampCollaborationValidatesIfInviteEmailIsValid() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => 'not an email',
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'inviteEmail: This value is not a valid email address.',
        ]);
    }

    public function testCreateCampCollaborationValidatesInviteEmailLength() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => str_repeat('a', 128).'@test.com',
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'inviteEmail: This value is too long. It should have 128 characters or less.',
        ]);
    }

    public function testCreateCampCollaborationTrimsInviteEmail() {
        static::createClientWithCredentials()->request(
            'POST',
            '/camp_collaborations',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'inviteEmail' => " \tsomeone@example.com \t",
                    ],
                    ['user']
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'inviteEmail' => 'someone@example.com',
            '_links' => [
                'user' => null,
            ],
        ]));
    }

    public function testCreateCampCollaborationWithInviteEmailInsteadOfUserIsPossible() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => 'someone@example.com',
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'status' => 'invited',
            'inviteEmail' => 'someone@example.com',
            '_links' => [
                'user' => null,
            ],
            '_embedded' => [
                'user' => null,
            ],
        ]));
    }

    public function testCreateCampCollaborationWithDuplicateInviteEmailForSameCampFails() {
        $inviteEmail = static::$fixtures['campCollaboration4invited']->inviteEmail;
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => $inviteEmail,
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'inviteEmail: This inviteEmail is already present in the camp.',
        ]);
    }

    public function testCreateCampCollaborationWithSameInviteEmailForAnotherCampSucceeds() {
        $inviteEmail = static::$fixtures['campCollaboration4invited']->inviteEmail;
        static::createClientWithCredentials()->request(
            'POST',
            '/camp_collaborations',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'inviteEmail' => $inviteEmail,
                        'camp' => $this->getIriFor('camp2'),
                    ],
                    ['user']
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'status' => 'invited',
            'inviteEmail' => $inviteEmail,
            '_links' => [
                'user' => null,
            ],
            '_embedded' => [
                'user' => null,
            ],
        ]));
    }

    public function testCreateCampCollaborationWithInviteEmailOfExistingUserAttachesUser() {
        /** @var User $userunrelated */
        $userunrelated = static::$fixtures['user4unrelated'];
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => $userunrelated->getEmail(),
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'status' => 'invited',
            'inviteEmail' => null,
            '_links' => [],
            '_embedded' => [
                'user' => [
                    'displayName' => $userunrelated->getDisplayName(),
                ],
            ],
        ]));
    }

    public function testCreateCampCollaborationWithInviteEmailOfExistingUserWhichIsAlreadyInCampFails() {
        /** @var User $user2member */
        $user2member = static::$fixtures['user2member'];
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => $user2member->getEmail(),
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'user: This user or a user with this email address is already participating in the camp.',
        ]);
    }

    public function testCreateCampCollaborationSendsInviteEmail() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => 'someone@example.com',
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(201);
        self::assertEmailCount(1);
    }

    public function testCreateCampCollaborationValidatesMissingUserAndInviteEmail() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([], ['user'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'inviteEmail',
                    'message' => 'Either this value or user should not be null.',
                ],
                [
                    'propertyPath' => 'user',
                    'message' => 'Either this value or inviteEmail should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateCampCollaborationValidatesMissingUserAndInviteEmailAndIncludesTranslationInfo() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([], ['user'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'i18n' => [
                        'key' => 'app.validator.asserteitherisnull',
                        'parameters' => [
                            'other' => 'user',
                        ],
                        'translations' => [
                            'en' => 'Either this value or user should not be null.',
                            'de' => 'Dieser Wert und user dürfen nicht beide null sein.',
                            'fr' => 'Cette valeur ou user ne doit pas être nulle.',
                            'it' => 'Questo valore o user non deve essere nullo.',
                        ],
                    ],
                ],
                [
                    'i18n' => [
                        'key' => 'app.validator.asserteitherisnull',
                        'parameters' => [
                            'other' => 'inviteEmail',
                        ],
                        'translations' => [
                            'en' => 'Either this value or inviteEmail should not be null.',
                            'de' => 'Dieser Wert und inviteEmail dürfen nicht beide null sein.',
                            'fr' => 'Cette valeur ou inviteEmail ne doit pas être nulle.',
                            'it' => 'Questo valore o inviteEmail non deve essere nullo.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testCreateCampCollaborationValidatesIfUserAndInviteEmailAreNull() {
        static::createClientWithCredentials()->request(
            'POST',
            '/camp_collaborations',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'inviteEmail' => null,
                        'user' => null,
                    ],
                    []
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'inviteEmail',
                    'message' => 'Either this value or user should not be null.',
                    'code' => null,
                ],
                [
                    'propertyPath' => 'user',
                    'message' => 'Either this value or inviteEmail should not be null.',
                    'code' => null,
                ],
            ],
        ]);
    }

    public function testCreateCampCollaborationValidatesConflictingUserAndInviteEmail() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => 'someone@example.com',
            'user' => $this->getIriFor('user4unrelated'),
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'inviteEmail',
                    'message' => 'Either this value or user should be null.',
                ],
                [
                    'propertyPath' => 'user',
                    'message' => 'Either this value or inviteEmail should be null.',
                ],
            ],
        ]);
    }

    public function testCreateCampCollaborationValidatesMissingCamp() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([], ['camp'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateCampCollaborationDisallowsSettingStatus() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'status' => 'established',
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("status" is unknown).',
        ]);
    }

    public function testCreateCampCollaborationDisallowsSettingInviteKey() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteKey' => 'hash',
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("inviteKey" is unknown).',
        ]);
    }

    public function testCreateCampCollaborationDisallowsSettingInviteKeyHash() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteKeyHash' => 'hash',
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("inviteKeyHash" is unknown).',
        ]);
    }

    public function testCreateCampCollaborationValidatesMissingRole() {
        static::createClientWithCredentials()->request(
            'POST',
            '/camp_collaborations',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'inviteEmail' => 'someone@example.com',
                    ],
                    ['role', 'user']
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'role',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            CampCollaboration::class,
            '/camp_collaborations',
            'post',
            array_merge([
                'inviteEmail' => null,
                'user' => $this->getIriFor('user1manager'),
                'camp' => $this->getIriFor('camp1'),
            ], $attributes),
            ['status'],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            CampCollaboration::class,
            '/camp_collaborations',
            'get',
            array_merge([
                '_links' => [
                    'user' => ['href' => $this->getIriFor('user1manager')],
                    'camp' => ['href' => $this->getIriFor('camp1')],
                ],
                'status' => 'invited',
                'inviteEmail' => null,
            ], $attributes),
            ['user', 'camp'],
            $except
        );
    }
}
