<?php

namespace App\Tests\Api\Profiles;

use App\Entity\Profile;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateProfileTest extends ECampApiTestCase {
    public function testPatchProfileIsDeniedForAnonymousProfile() {
        $user = static::$fixtures['user1manager'];
        static::createBasicClient()->request('PATCH', '/profiles/'.$user->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchProfileIsDeniedForRelatedProfile() {
        $user2 = static::$fixtures['user2member'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$user2->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchProfileIsDeniedForUnrelatedProfile() {
        $user2 = static::$fixtures['user4unrelated'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$user2->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchProfileIsAllowedForSelf() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Linux',
            '_links' => [
                'self' => [
                    'href' => '/profiles/'.$profile->getId(),
                ],
                'user' => [
                    'href' => $this->getIriFor('user1manager'),
                ],
            ],
        ]);
    }

    public function testPatchProfileIsAllowedForSelfIfSelfHasNoCampCollaborations() {
        $profile = static::$fixtures['profileWithoutCampCollaborations'];
        static::createClientWithCredentials(['username' => $profile->user->getUserName()])
            ->request(
                'PATCH',
                '/profiles/'.$profile->getId(),
                [
                    'json' => ['nickname' => 'Linux'],
                    'headers' => ['Content-Type' => 'application/merge-patch+json'],
                ]
            )
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Linux',
            '_links' => [
                'self' => [
                    'href' => '/profiles/'.$profile->getId(),
                ],
                'user' => [
                    'href' => $this->getIriFor('userWithoutCampCollaborations'),
                ],
            ],
        ]);
    }

    public function testPatchProfileTrimsEmail() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'email' => ' trimmed@example.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'email' => 'trimmed@example.com',
        ]);
    }

    public function testPatchProfileValidatesBlankEmail() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'email' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
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

    public function testPatchProfileValidatesInvalidEmail() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'email' => 'hello@sunrise',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
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

    public function testPatchProfileValidatesLongEmail() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'email' => 'test-with-a-very-long-email-address-which-is-not-really-realistic@example.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
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

    public function testPatchProfileValidatesDuplicateEmail() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'email' => static::$fixtures['user2member']->getEmail(),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
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

    public function testPatchProfileTrimsFirstThenValidatesDuplicateEmail() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'email' => ' '.static::$fixtures['user2member']->getEmail(),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
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

    public function testPatchProfileDisallowsChangingProfilename() {
        /** @var Profile $profile */
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'username' => 'bi-pi',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("username" is unknown).',
        ]);
    }

    public function testPatchProfileTrimsFirstname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'firstname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'firstname' => 'Hello',
        ]);
    }

    public function testPatchProfileCleansHTMLFromFirstname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'firstname' => '<script>alert(1)</script>Hello',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'firstname' => 'Hello',
        ]);
    }

    public function testPatchProfileTrimsSurname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'surname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'surname' => 'Hello',
        ]);
    }

    public function testPatchProfileCleansHTMLFromSurname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'surname' => '<script>alert(1)</script>Hello',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'surname' => 'Hello',
        ]);
    }

    public function testPatchProfileTrimsNickname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'nickname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Hello',
        ]);
    }

    public function testPatchProfileCleansHTMLFromNickname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'nickname' => '<script>alert(1)</script>Hello',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Hello',
        ]);
    }

    public function testPatchProfileTrimsLanguage() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'language' => "\tde ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'language' => 'de',
        ]);
    }

    public function testPatchProfileValidatesInvalidLanguage() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'language' => 'französisch',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'language',
                    'message' => 'The value you selected is not a valid choice.',
                ],
            ],
        ]);
    }

    public function testPatchProfileDoesNotAllowPatchingUser() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request(
            'PATCH',
            '/profiles/'.$profile->getId(),
            [
                'json' => [
                    'user' => [
                        'password' => 'an 8 digit long password',
                    ],
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Extra attributes are not allowed ("user" is unknown).',
        ]);
    }
}
