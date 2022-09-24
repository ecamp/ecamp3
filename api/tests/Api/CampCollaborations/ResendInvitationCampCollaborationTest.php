<?php

namespace App\Tests\Api\CampCollaborations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ResendInvitationCampCollaborationTest extends ECampApiTestCase {
    public const RESEND_INVITATION = CampCollaboration::RESEND_INVITATION;

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testResendInvitationSuccessfulWhenUserIsManager() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        static::createClientWithCredentials(['username' => static::$fixtures['user1manager']->getUsername()])->request(
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
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testResendInvitationSuccessfulWhenUserIsMember() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])->request(
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
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testResendInvitationSuccessfulWhenInvitedUserExists() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration6invitedWithUser'];
        static::createClientWithCredentials(['username' => static::$fixtures['user1manager']->getUsername()])->request(
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
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenNotAuthenticated() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenUserNotPartOfCamp() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenUserIsInvited() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration6invitedWithUser'];
        static::createClientWithCredentials(['username' => static::$fixtures['user6invited']->getUsername()])
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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenUserIsInactive() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testResendInvitationFailsWhenUserIsGuest() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
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
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function testResendInvitationFailsWhenCampCollaborationIsNotInStatusInvited() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration2member'];
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
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function testResendInvitationFailsWithAdditionalAttribute() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration2member'];
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
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
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
