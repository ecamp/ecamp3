<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @internal
 */
class ResendInvitationCampCollaborationTest extends ECampApiTestCase {
    public const RESEND_INVITATION = CampCollaboration::RESEND_INVITATION;

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testResendInvitationSuccessfulWhenUserIsManager() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClientWithCredentials(['email' => static::$fixtures['user1manager']->getEmail()])->request(
            'PATCH',
            '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'inviteEmail' => $campCollaboration->inviteEmail,
            'status' => $campCollaboration->status,
            'role' => $campCollaboration->role,
        ]);
        self::assertEmailCount(1);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testResendInvitationSuccessfulWhenUserIsMember() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])->request(
            'PATCH',
            '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'inviteEmail' => $campCollaboration->inviteEmail,
            'status' => $campCollaboration->status,
            'role' => $campCollaboration->role,
        ]);
        self::assertEmailCount(1);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testResendInvitationSuccessfulWhenInvitedUserExists() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        static::createClientWithCredentials(['email' => static::$fixtures['user1manager']->getEmail()])->request(
            'PATCH',
            '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'inviteEmail' => $campCollaboration->inviteEmail,
            'status' => $campCollaboration->status,
            'role' => $campCollaboration->role,
        ]);
        self::assertEmailCount(1);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenNotAuthenticated() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClient()->request(
            'PATCH',
            '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenUserNotPartOfCamp() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request(
                'PATCH',
                '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
                [
                    'json' => [],
                    'headers' => ['Content-Type' => 'application/merge-patch+json'],
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenUserIsInvited() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request(
                'PATCH',
                '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
                [
                    'json' => [],
                    'headers' => ['Content-Type' => 'application/merge-patch+json'],
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenUserIsInactive() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request(
                'PATCH',
                '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
                [
                    'json' => [],
                    'headers' => ['Content-Type' => 'application/merge-patch+json'],
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenUserIsGuest() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request(
                'PATCH',
                '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
                [
                    'json' => [],
                    'headers' => ['Content-Type' => 'application/merge-patch+json'],
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testResendInvitationFailsWhenCampCollaborationIsNotInStatusInvited() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2member');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testResendInvitationFailsWithAdditionalAttribute() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2member');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/camp_collaborations/'.$campCollaboration->getId().'/'.self::RESEND_INVITATION,
            [
                'json' => [
                    'status' => CampCollaboration::STATUS_INVITED,
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testResendInvitationFailsWhenCampCollaborationIsNotFound() {
        static::createClientWithCredentials()->request(
            'PATCH',
            '/camp_collaborations/doesNotExist/'.self::RESEND_INVITATION,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(404);
    }
}
