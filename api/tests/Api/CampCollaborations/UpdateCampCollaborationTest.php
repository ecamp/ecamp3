<?php

namespace App\Tests\Api\CampCollaborations;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateCampCollaborationTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testPatchCampCollaborationIsAllowedForCollaborator() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => 'inactive',
            'role' => 'guest',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => 'inactive',
            'role' => 'guest',
        ]);
    }

    public function testPatchCampCollaborationDisallowsChangingInviteEmail() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'inviteEmail' => 'some@thing.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("inviteEmail" is unknown).',
        ]);
    }

    public function testPatchCampCollaborationDisallowsChangingUser() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'user' => $this->getIriFor('user2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("user" is unknown).',
        ]);
    }

    public function testPatchCampCollaborationDisallowsChangingCamp() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'camp' => $this->getIriFor('camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" is unknown).',
        ]);
    }

    public function testPatchCampCollaborationValidatesInvalidStatus() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => 'expelled',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'status',
                    'message' => 'The value you selected is not a valid choice.',
                ],
            ],
        ]);
    }

    public function testPatchCampCollaborationValidatesInvalidRole() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'role' => 'admin',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'role',
                    'message' => 'The value you selected is not a valid choice.',
                ],
            ],
        ]);
    }
}
