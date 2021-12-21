<?php

namespace App\Tests\Api\CampCollaborations;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCampCollaborationTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests
    // TODO create a camp collaboration for someone else
    // TODO add test creating a collaboration with user reference once a user can see other users (https://github.com/ecamp/ecamp3/pull/2241)

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
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
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
            'inviteEmail' => $userunrelated->email,
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'status' => 'invited',
            'inviteEmail' => null,
            '_links' => [],
            '_embedded' => [
                'user' => [
                    'username' => $userunrelated->username,
                ],
            ],
        ]));
    }

    public function testCreateCampCollaborationWithInviteEmailOfExistingUserWhichIsAlreadyInCampFails() {
        /** @var User $user2member */
        $user2member = static::$fixtures['user2member'];
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => $user2member->email,
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'user: This user is already present in the camp.',
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

    public function testCreateCampCollaborationValidatesConflictingUserAndInviteEmail() {
        $this->markTestIncomplete('This test needs https://github.com/ecamp/ecamp3/pull/2241');
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
            OperationType::COLLECTION,
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
            OperationType::ITEM,
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
