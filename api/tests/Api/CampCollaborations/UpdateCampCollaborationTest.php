<?php

namespace App\Tests\Api\CampCollaborations;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateCampCollaborationTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testPatchCampCollaborationIsDeniedForAnonymousUser() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createBasicClient()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => 'inactive',
            'role' => 'guest',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchCampCollaborationIsDeniedForUnrelatedUser() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
                'status' => 'inactive',
                'role' => 'guest',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchCampCollaborationIsDeniedForInactiveCollaborator() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
                'status' => 'inactive',
                'role' => 'guest',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchCampCollaborationIsDeniedForGuest() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
                'status' => 'inactive',
                'role' => 'guest',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testOwnPatchCampCollaborationIsAllowedForGuest() {
        $campCollaboration = static::$fixtures['campCollaboration3guest'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
                'status' => 'inactive',
                'role' => 'guest',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => 'inactive',
            'role' => 'guest',
        ]);
    }

    public function testPatchCampCollaborationIsAllowedForMember() {
        $campCollaboration = static::$fixtures['campCollaboration1manager'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
                'status' => 'inactive',
                'role' => 'guest',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => 'inactive',
            'role' => 'guest',
        ]);
    }

    public function testPatchCampCollaborationIsAllowedForManager() {
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

    public function testPatchCampCollaborationInCampPrototypeIsDeniedForUnrelatedUser() {
        $campCollaboration = static::$fixtures['campCollaboration1campPrototype'];
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => 'inactive',
            'role' => 'guest',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
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
            'user' => $this->getIriFor('user2member'),
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
