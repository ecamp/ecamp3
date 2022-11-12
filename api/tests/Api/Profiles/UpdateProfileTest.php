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
        static::createClientWithCredentials(['email' => $profile->user->getEmail()])
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

    public function testPatchProfileDisallowsChangingEmail() {
        /** @var Profile $profile */
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'email' => 'e@mail.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("email" is unknown).',
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

    public function testPatchProfileCleansForbiddenCharactersFromFirstname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'firstname' => "\n\tHello",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'firstname' => 'Hello',
        ]);
    }

    public function testPatchProfileValidatesFirstnameMaxLength() {
        /** @var Profile $profile */
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'firstname' => str_repeat('a', 65),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => 'firstname: This value is too long. It should have 64 characters or less.',
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

    public function testPatchProfileCleansForbiddenCharactersFromSurname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'surname' => "\n\tHello",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'surname' => 'Hello',
        ]);
    }

    public function testPatchProfileValidatesSurnameMaxLength() {
        /** @var Profile $profile */
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'surname' => str_repeat('a', 65),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => 'surname: This value is too long. It should have 64 characters or less.',
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

    public function testPatchProfileCleansForbiddenCharactersFromNickname() {
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'nickname' => "\n\tHello",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Hello',
        ]);
    }

    public function testPatchProfileValidatesNicknameMaxLength() {
        /** @var Profile $profile */
        $profile = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'nickname' => str_repeat('a', 33),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => 'nickname: This value is too long. It should have 32 characters or less.',
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

    /**
     * @dataProvider notWriteableProfileProperties
     */
    public function testNotWriteableProperties(string $property) {
        $user = static::$fixtures['profile1manager'];
        static::createClientWithCredentials()->request(
            'PATCH',
            '/profiles/'.$user->getId(),
            [
                'json' => [
                    $property => 'something',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
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
            'untrustedEmailKeyHash' => ['untrustedEmailKeyHash'],
            'googleId' => ['googleId'],
            'pbsmidataId' => ['pbsmidataId'],
            'roles' => ['roles'],
            'user' => ['user'],
        ];
    }
}
