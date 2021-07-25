<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCampCollaborationTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateCampCollaborationIsAllowedForCollaborator() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateCampCollaborationWithInviteEmailInsteadOfUserIsPossible() {
        static::createClientWithCredentials()->request('POST', '/camp_collaborations', ['json' => $this->getExampleWritePayload([
            'inviteEmail' => 'someone@example.com',
        ], ['user'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload([
            'status' => 'invited',
            'inviteEmail' => 'someone@example.com',
            '_links' => [],
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
            'user' => $this->getIriFor('user1'),
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
            array_merge([
                'inviteEmail' => null,
                'user' => $this->getIriFor('user1'),
                'camp' => $this->getIriFor('camp1'),
            ], $attributes),
            ['status'],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            CampCollaboration::class,
            array_merge([
                '_links' => [
                    'user' => ['href' => $this->getIriFor('user1')],
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
