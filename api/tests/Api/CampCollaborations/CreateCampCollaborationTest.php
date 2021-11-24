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
            ->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
                'user' => $this->getIriFor('user2member'),
            ])])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'inviteEmail' => null,
            '_links' => [
                'user' => ['href' => $this->getIriFor('user2member')],
            ],
        ]));
    }

    public function testCreateCampCollaborationIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
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
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => 'someone@example.com',
            'user' => $this->getIriFor('user1manager'),
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
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([], ['role'])]);

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
