<?php

namespace App\Tests\Api\Profiles;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\Profile;
use App\Entity\User;
use App\Service\MailService;
use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\BrowserKit\Cookie;

/**
 * @internal
 */
class UpdateProfileTest extends ECampApiTestCase {
    public function testPatchProfileIsDeniedForAnonymousProfile() {
        $user = static::getFixture('user1manager');
        static::createBasicClient()->request('PATCH', '/profiles/'.$user->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPatchProfileIsDeniedForRelatedProfile() {
        $user2 = static::getFixture('user2member');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$user2->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchProfileIsDeniedForUnrelatedProfile() {
        $user2 = static::getFixture('user4unrelated');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$user2->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchProfileIsAllowedForSelf() {
        $profile = static::getFixture('profile1manager');
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
        $profile = static::getFixture('profileWithoutCampCollaborations');
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
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'email' => 'e@mail.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("email" is unknown).',
        ]);
    }

    public function testPatchProfileValidatesChangedEmail() {
        $client = static::createClientWithCredentials();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $untrustedEmailKey = null;
        $mailServiceMock = $this->createMock(MailService::class);
        $mailServiceMock->expects($this->once())
            ->method('sendEmailVerificationMail')
            ->willReturnCallback(
                function ($user, $profile) use (&$untrustedEmailKey): void {
                    $untrustedEmailKey = $profile->untrustedEmailKey;
                }
            )
        ;
        $this->getContainer()->set(MailService::class, $mailServiceMock);

        /** @var Profile $profile */
        $profile = static::getFixture('profile1manager');

        $client->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'newEmail' => 'new@example.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);

        // when
        $client->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'untrustedEmailKey' => $untrustedEmailKey,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        // then
        $this->assertResponseStatusCodeSame(200);
        $profile = $this->getEntityManager()->find(Profile::class, $profile->getId());
        $this->assertEquals('new@example.com', $profile->email);
    }

    public function testPatchProfileDoesNotClaimPersonalInvitation() {
        $client = static::createClientWithCredentials();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $camp = $this->getEntityManager()->find(Camp::class, static::getFixture('campUnrelated')->getId());
        $camp2 = $this->getEntityManager()->find(Camp::class, static::getFixture('campPrototype')->getId());

        // create an invitation which could be claimed by the user
        $invitation1 = new CampCollaboration();
        $invitation1->camp = $camp;
        $invitation1->status = CampCollaboration::STATUS_INVITED;
        $invitation1->inviteEmail = 'test@example.com';
        $invitation1->inviteKeyHash = '1234123412341234';
        $invitation1->role = CampCollaboration::ROLE_MANAGER;
        $this->getEntityManager()->persist($invitation1);

        // create a rejected invitation which will not be claimed by the user
        $invitation2 = new CampCollaboration();
        $invitation2->camp = $camp2;
        $invitation2->status = CampCollaboration::STATUS_INACTIVE;
        $invitation2->inviteEmail = 'test@example.com';
        $invitation2->inviteKeyHash = '2341234123412341';
        $invitation2->role = CampCollaboration::ROLE_MANAGER;
        $this->getEntityManager()->persist($invitation2);

        // create an unrelated invitation which will not be claimed by the user
        $invitation3 = new CampCollaboration();
        $invitation3->camp = $camp;
        $invitation3->status = CampCollaboration::STATUS_INVITED;
        $invitation3->inviteEmail = 'someone-else@example.com';
        $invitation3->inviteKeyHash = '3412341234123412';
        $invitation3->role = CampCollaboration::ROLE_MANAGER;
        $this->getEntityManager()->persist($invitation3);

        $this->getEntityManager()->flush();

        /** @var Profile $profile */
        $profile = static::getFixture('profile1manager');

        // when
        $client->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'nickname' => 'Linux',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);

        // then
        $client->request('GET', '/personal_invitations');

        // User has one personal invitation waiting for them
        $this->assertJsonContains([
            'totalItems' => 0,
        ]);
    }

    public function testActivatingEmailClaimsPersonalInvitation() {
        $client = static::createClientWithCredentials();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $camp = $this->getEntityManager()->find(Camp::class, static::getFixture('campUnrelated')->getId());
        $camp2 = $this->getEntityManager()->find(Camp::class, static::getFixture('campPrototype')->getId());

        // create an invitation which will be claimed by the user
        $invitation1 = new CampCollaboration();
        $invitation1->camp = $camp;
        $invitation1->status = CampCollaboration::STATUS_INVITED;
        $invitation1->inviteEmail = 'new@example.com';
        $invitation1->inviteKeyHash = '1234123412341234';
        $invitation1->role = CampCollaboration::ROLE_MANAGER;
        $this->getEntityManager()->persist($invitation1);

        // create a rejected invitation which will not be claimed by the user
        $invitation2 = new CampCollaboration();
        $invitation2->camp = $camp2;
        $invitation2->status = CampCollaboration::STATUS_INACTIVE;
        $invitation2->inviteEmail = 'new@example.com';
        $invitation2->inviteKeyHash = '2341234123412341';
        $invitation2->role = CampCollaboration::ROLE_MANAGER;
        $this->getEntityManager()->persist($invitation2);

        // create an unrelated invitation which will not be claimed by the user
        $invitation3 = new CampCollaboration();
        $invitation3->camp = $camp;
        $invitation3->status = CampCollaboration::STATUS_INVITED;
        $invitation3->inviteEmail = 'someone-else@example.com';
        $invitation3->inviteKeyHash = '3412341234123412';
        $invitation3->role = CampCollaboration::ROLE_MANAGER;
        $this->getEntityManager()->persist($invitation3);

        $this->getEntityManager()->flush();

        $untrustedEmailKey = null;
        $mailServiceMock = $this->createMock(MailService::class);
        $mailServiceMock->expects($this->once())->method('sendEmailVerificationMail')->willReturnCallback(function ($user, $profile) use (&$untrustedEmailKey): void {
            $untrustedEmailKey = $profile->untrustedEmailKey;
        });
        $this->getContainer()->set(MailService::class, $mailServiceMock);

        /** @var Profile $profile */
        $profile = static::getFixture('profile1manager');

        $client->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'newEmail' => 'new@example.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);

        // when
        $client->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'untrustedEmailKey' => $untrustedEmailKey,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);

        // then

        // we need to log in again after changing the email address, because the login email is in the JWT token
        $profile = $this->getEntityManager()->find(Profile::class, $profile->getId());
        $jwtToken = static::getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($profile->user);
        $lastPeriodPosition = strrpos($jwtToken, '.');
        $jwtHeaderAndPayload = substr($jwtToken, 0, $lastPeriodPosition);
        $jwtSignature = substr($jwtToken, $lastPeriodPosition + 1);
        $cookies = $client->getCookieJar();
        $cookies->set(new Cookie('example_com_jwt_hp', $jwtHeaderAndPayload, null, null, 'localhost', false, false, false, 'strict'));
        $cookies->set(new Cookie('example_com_jwt_s', $jwtSignature, null, null, 'localhost', false, true, false, 'strict'));

        $client->request('GET', '/personal_invitations');

        // User has one personal invitation waiting for them
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [
                    ['href' => "/personal_invitations/{$invitation1->getId()}"],
                ],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
    }

    public function testActivatingEmailClaimingPersonalInvitationHandlesUniqueConstraintViolationGracefully() {
        $client = static::createClientWithCredentials();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $camp = $this->getEntityManager()->find(Camp::class, static::getFixture('camp1')->getId());

        // create an invitation which will be claimed by the user, in a camp where the user already collaborates
        $invitation1 = new CampCollaboration();
        $invitation1->camp = $camp;
        $invitation1->status = CampCollaboration::STATUS_INVITED;
        $invitation1->inviteEmail = 'new@example.com';
        $invitation1->inviteKeyHash = '1234123412341234';
        $invitation1->role = CampCollaboration::ROLE_MANAGER;
        $this->getEntityManager()->persist($invitation1);

        $this->getEntityManager()->flush();

        $untrustedEmailKey = null;
        $mailServiceMock = $this->createMock(MailService::class);
        $mailServiceMock->expects($this->once())->method('sendEmailVerificationMail')->willReturnCallback(function ($user, $profile) use (&$untrustedEmailKey): void {
            $untrustedEmailKey = $profile->untrustedEmailKey;
        });
        $this->getContainer()->set(MailService::class, $mailServiceMock);

        /** @var Profile $profile */
        $profile = static::getFixture('profile1manager');

        $client->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'newEmail' => 'new@example.com',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);

        // when
        $client->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'untrustedEmailKey' => $untrustedEmailKey,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);

        // then

        // we need to log in again after changing the email address, because the login email is in the JWT token
        $profile = $this->getEntityManager()->find(Profile::class, $profile->getId());
        $jwtToken = static::getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($profile->user);
        $lastPeriodPosition = strrpos($jwtToken, '.');
        $jwtHeaderAndPayload = substr($jwtToken, 0, $lastPeriodPosition);
        $jwtSignature = substr($jwtToken, $lastPeriodPosition + 1);
        $cookies = $client->getCookieJar();
        $cookies->set(new Cookie('example_com_jwt_hp', $jwtHeaderAndPayload, null, null, 'localhost', false, false, false, 'strict'));
        $cookies->set(new Cookie('example_com_jwt_s', $jwtSignature, null, null, 'localhost', false, true, false, 'strict'));

        $client->request('GET', '/personal_invitations');

        // User has one personal invitation waiting for them
        $this->assertJsonContains([
            'totalItems' => 0,
        ]);
    }

    public function testPatchProfileTrimsFirstname() {
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'firstname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'firstname' => 'Hello',
        ]);
    }

    public function testPatchProfileCleansForbiddenCharactersFromFirstname() {
        $profile = static::getFixture('profile1manager');
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
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'firstname' => str_repeat('a', 65),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => 'firstname: This value is too long. It should have 64 characters or less.',
        ]);
    }

    public function testPatchProfileTrimsSurname() {
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'surname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'surname' => 'Hello',
        ]);
    }

    public function testPatchProfileCleansForbiddenCharactersFromSurname() {
        $profile = static::getFixture('profile1manager');
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
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'surname' => str_repeat('a', 65),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => 'surname: This value is too long. It should have 64 characters or less.',
        ]);
    }

    public function testPatchProfileTrimsNickname() {
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'nickname' => "\tHello ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'nickname' => 'Hello',
        ]);
    }

    public function testPatchProfileCleansForbiddenCharactersFromNickname() {
        $profile = static::getFixture('profile1manager');
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
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'nickname' => str_repeat('a', 33),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => 'nickname: This value is too long. It should have 32 characters or less.',
        ]);
    }

    public function testPatchProfileTrimsLanguage() {
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'language' => "\tde ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'language' => 'de',
        ]);
    }

    public function testPatchProfileValidatesInvalidColor() {
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
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
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
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
        $profile = static::getFixture('profile1manager');
        static::createClientWithCredentials()->request('PATCH', '/profiles/'.$profile->getId(), ['json' => [
            'abbreviation' => $abbreviation,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'abbreviation' => $abbreviation,
        ]);
    }

    public static function validAbbreviations(): \Iterator {
        yield ['AB'];

        yield ['33'];

        yield ['X4'];

        yield ['✅😊'];

        yield ['🧑🏿‍🚀🧑🏼‍🔧'];
    }

    public function testPatchProfileValidatesInvalidLanguage() {
        $profile = static::getFixture('profile1manager');
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
        $profile = static::getFixture('profile1manager');
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

    #[DataProvider('notWriteableProfileProperties')]
    public function testNotWriteableProperties(string $property) {
        $user = static::getFixture('profile1manager');
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

    public static function notWriteableProfileProperties(): \Iterator {
        yield 'untrustedEmailKeyHash' => ['untrustedEmailKeyHash'];

        yield 'googleId' => ['googleId'];

        yield 'pbsmidataId' => ['pbsmidataId'];

        yield 'roles' => ['roles'];

        yield 'user' => ['user'];
    }
}
