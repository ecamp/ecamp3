<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @internal
 */
class UpdateCampCollaborationTest extends ECampApiTestCase {
    public function testPatchCampCollaborationIsDeniedForAnonymousUser() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
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
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials(['email' => static::getFixture('user4unrelated')->getEmail()])
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
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials(['email' => static::getFixture('user5inactive')->getEmail()])
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
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials(['email' => static::getFixture('user3guest')->getEmail()])
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

    public function testPatchOwnCampCollaborationIsAllowedForGuest() {
        $campCollaboration = static::getFixture('campCollaboration3guest');
        static::createClientWithCredentials(['email' => $campCollaboration->getEmail()])
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

    public function testPatchOwnCampCollaborationToMemberIsRejectedForGuest() {
        $campCollaboration = static::getFixture('campCollaboration3guest');
        static::createClientWithCredentials(['email' => $campCollaboration->getEmail()])
            ->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
                'role' => 'member',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchOwnCampCollaborationToManagerIsRejectedForGuest() {
        $campCollaboration = static::getFixture('campCollaboration3guest');
        static::createClientWithCredentials(['email' => $campCollaboration->getEmail()])
            ->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
                'role' => 'manager',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchCampCollaborationIsAllowedForMember() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials(['email' => static::getFixture('user2member')->getEmail()])
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

    /**
     * Silly, but for now members can change the collaborations in the api.
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws DecodingExceptionInterface|TransportExceptionInterface
     */
    public function testPatchOwnCampCollaborationToManagerIsAllowedForMember() {
        $campCollaboration = static::getFixture('campCollaboration2member');
        static::createClientWithCredentials(['email' => $campCollaboration->getEmail()])
            ->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
                'role' => 'manager',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'role' => 'manager',
        ]);
    }

    public function testPatchOwnStatusOfCampCollaborationIsAllowedForManager() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => 'inactive',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => 'inactive',
        ]);
    }

    public function testPatchOwnRoleToMemberOfCampCollaborationIsAllowedForManager() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'role' => 'member',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'role' => 'member',
        ]);
    }

    public function testPatchOwnRoleToGuestOfCampCollaborationIsAllowedForMember() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'role' => CampCollaboration::ROLE_GUEST,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'role' => 'guest',
        ]);
    }

    public function testPatchOwnRoleToGuestOfCampCollaborationIsAllowedForManager() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'role' => 'guest',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'role' => 'guest',
        ]);
    }

    public function testPatchOwnStatusAndRoleCampCollaborationIsAcceptedForManager() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
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

    public function testPatchOwnStatusAndRoleOfCampCollaborationIsAcceptedForMember() {
        $campCollaboration = static::getFixture('campCollaboration2member');
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
        $campCollaboration = static::getFixture('campCollaboration1campPrototype');
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

    public function testPatchCampCollaborationInSharedCampIsDeniedForUnrelatedUser() {
        $campCollaboration = static::getFixture('campCollaboration1campShared');
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

    public function testPatchCampCollaborationInSharedCampIsDeniedForInactiveUser() {
        $campCollaboration = static::getFixture('campCollaboration1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => 'inactive',
            'role' => 'guest',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchCampCollaborationInSharedCampIsDeniedForInvitedUser() {
        $campCollaboration = static::getFixture('campCollaboration1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => 'inactive',
            'role' => 'guest',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchCampCollaborationRoleWithoutUserIsAllowed() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');

        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), [
            'json' => [
                'role' => CampCollaboration::ROLE_GUEST,
            ],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'role' => CampCollaboration::ROLE_GUEST,
        ]);
    }

    public function testPatchCampCollaborationRoleFromGuestToManagerWithoutUserIsAllowed() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');

        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), [
            'json' => [
                'role' => CampCollaboration::ROLE_MANAGER,
            ],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), [
            'json' => [
                'role' => CampCollaboration::ROLE_MANAGER,
            ],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        // This should succeed without errors
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'role' => 'manager',
        ]);
    }

    public function testPatchCampCollaborationDisallowsSettingUserToNull() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');

        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), [
            'json' => [
                'user' => null,
            ],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("user" is unknown).',
        ]);
    }

    public function testPatchCampCollaborationDisallowsChangingInviteEmail() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'inviteEmail' => 'some@thing.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("inviteEmail" is unknown).',
        ]);
    }

    public function testPatchCampCollaborationDisallowsChangingUser() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'user' => $this->getIriFor('user2member'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("user" is unknown).',
        ]);
    }

    public function testPatchCampCollaborationDisallowsChangingCamp() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'camp' => $this->getIriFor('camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" is unknown).',
        ]);
    }

    public function testPatchCampCollaborationValidatesInvalidStatus() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
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

    public function testAllowPatchStatusFromInvitedToInactive() {
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => CampCollaboration::STATUS_INACTIVE,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => CampCollaboration::STATUS_INACTIVE,
        ]);
    }

    public function testRejectsPatchStatusFromInvitedToEstablished() {
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => CampCollaboration::STATUS_ESTABLISHED,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testAllowPatchStatusFromEstablishedToInactive() {
        $campCollaboration = static::getFixture('campCollaboration2member');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => CampCollaboration::STATUS_INACTIVE,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => CampCollaboration::STATUS_INACTIVE,
        ]);
    }

    public function testRejectsPatchStatusFromEstablishedToInvited() {
        $campCollaboration = static::getFixture('campCollaboration2member');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => CampCollaboration::STATUS_INVITED,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testAllowPatchStatusFromInactiveToInvited() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => CampCollaboration::STATUS_INVITED,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => CampCollaboration::STATUS_INVITED,
        ]);
    }

    public function testRejectsPatchStatusFromInactiveToEstablished() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => CampCollaboration::STATUS_ESTABLISHED,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testPatchCampCollaborationValidatesInvalidRole() {
        $campCollaboration = static::getFixture('campCollaboration1manager');
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

    public function testPatchStatusFromInactiveToInvitedSendsInviteEmail() {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'status' => CampCollaboration::STATUS_INVITED,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => 'invited',
        ]);
        $this->assertEmailCount(1);
    }

    public function testPatchCampCollaborationValidatesInvalidColor() {
        $campCollaboration = static::getFixture('campCollaboration3guest');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'color' => 'red',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'color',
                    'message' => 'This value is not valid.',
                ],
            ],
        ]);
    }

    #[DataProvider('invalidAbbreviations')]
    public function testPatchCampCollaborationValidatesInvalidAbbreviation($abbreviation) {
        $campCollaboration = static::getFixture('campCollaboration3guest');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'abbreviation' => $abbreviation,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'abbreviation',
                    'message' => 'This value is too long. It should have 2 characters or less.',
                ],
            ],
        ]);
    }

    public static function invalidAbbreviations(): \Iterator {
        yield ['ABC'];

        yield ['D3C'];

        yield ['🧑🏿‍🚀🙋🏼‍♀️😊'];
    }

    #[DataProvider('validAbbreviations')]
    public function testPatchCampCollaborationValidatesValidAbbreviation($abbreviation) {
        $campCollaboration = static::getFixture('campCollaboration5inactive');
        static::createClientWithCredentials()->request('PATCH', '/camp_collaborations/'.$campCollaboration->getId(), ['json' => [
            'abbreviation' => $abbreviation,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'abbreviation' => $abbreviation,
        ]);
    }

    public static function validAbbreviations(): \Iterator {
        yield ['A'];

        yield ['33'];

        yield ['X4'];

        yield ['✅😊'];

        yield ['🧑🏿‍🚀🧑🏼‍🔧'];

        yield ['⚜️'];
    }

    #[DataProvider('usersWhichCanEditCampCollaborations')]
    public function testRejectsPatchStatusFromEstablishedToInactiveIfNoManagerWouldBeInCamp(string $userFixtureName) {
        $campCollaboration = static::getFixture('campCollaboration1camp2manager');

        /** @var User $user */
        $user = static::getFixture($userFixtureName);
        static::createClientWithCredentials(['email' => $user->getEmail()])
            ->request(
                'PATCH',
                '/camp_collaborations/'.$campCollaboration->getId(),
                [
                    'json' => [
                        'status' => CampCollaboration::STATUS_INACTIVE,
                    ],
                    'headers' => ['Content-Type' => 'application/merge-patch+json'],
                ]
            )
        ;
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp.collaborations',
                    'message' => 'must have at least one manager.',
                ],
            ],
        ]);
    }

    #[DataProvider('usersWhichCanEditCampCollaborations')]
    public function testRejectsPatchRoleToMemberIfNoManagerWouldBeInCamp(string $userFixtureName) {
        $campCollaboration = static::getFixture('campCollaboration1camp2manager');

        /** @var User $user */
        $user = static::getFixture($userFixtureName);
        static::createClientWithCredentials(['email' => $user->getEmail()])
            ->request(
                'PATCH',
                '/camp_collaborations/'.$campCollaboration->getId(),
                [
                    'json' => [
                        'role' => CampCollaboration::ROLE_MEMBER,
                    ],
                    'headers' => ['Content-Type' => 'application/merge-patch+json'],
                ]
            )
        ;
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp.collaborations',
                    'message' => 'must have at least one manager.',
                ],
            ],
        ]);
    }

    #[DataProvider('usersWhichCanEditCampCollaborations')]
    public function testRejectsPatchRoleToGuestIfNoManagerWouldBeInCamp(string $userFixtureName) {
        $campCollaboration = static::getFixture('campCollaboration1camp2manager');

        /** @var User $user */
        $user = static::getFixture($userFixtureName);
        static::createClientWithCredentials(['email' => $user->getEmail()])
            ->request(
                'PATCH',
                '/camp_collaborations/'.$campCollaboration->getId(),
                [
                    'json' => [
                        'role' => CampCollaboration::ROLE_GUEST,
                    ],
                    'headers' => ['Content-Type' => 'application/merge-patch+json'],
                ]
            )
        ;
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp.collaborations',
                    'message' => 'must have at least one manager.',
                ],
            ],
        ]);
    }

    public static function usersWhichCanEditCampCollaborations(): \Iterator {
        yield 'user1manager' => ['user1manager'];

        yield 'user2member' => ['user2member'];
    }
}
