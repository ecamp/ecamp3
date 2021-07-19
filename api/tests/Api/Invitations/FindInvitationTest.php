<?php

namespace App\Tests\Api\Invitations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class FindInvitationTest extends ECampApiTestCase {
    const OPTIONS = ['headers' => ['Accept' => 'application/hal+json']];

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function testFindInvitationWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration1invited'];
        static::createClient()->request('GET', "/invitations/{$campCollaboration->inviteKey}/find", self::OPTIONS);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => null,
            'userAlreadyInCamp' => null,
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testFindInvitationWhenLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration2invitedCampUnrelated'];
        static::createClientWithCredentials()->request('GET', "/invitations/{$campCollaboration->inviteKey}/find", self::OPTIONS);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => 'test-user',
            'userAlreadyInCamp' => false,
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testUserAlreadyInCampTrueWhenUserAlreadyInCamp() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration1invited'];
        static::createClientWithCredentials()->request('GET', "/invitations/{$campCollaboration->inviteKey}/find", self::OPTIONS);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => 'test-user',
            'userAlreadyInCamp' => true,
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    public function testNotFoundWhenInvitekeyDoesNotMatch() {
        static::createClient()->request('GET', '/invitations/notExisting/find', self::OPTIONS);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testNotFoundWhenNoInviteKey() {
        static::createClient()->request('GET', '/invitations/find', self::OPTIONS);
        $this->assertResponseStatusCodeSame(404);
    }
}
