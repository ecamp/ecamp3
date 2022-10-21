<?php

namespace App\Tests\Api\Invitations;

use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class FindInvitationTest extends ECampApiTestCase {
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function testFindInvitationWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        static::createBasicClient()->request('GET', "/invitations/{$campCollaboration->inviteKey}/find");
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
        static::createClientWithCredentials()->request('GET', "/invitations/{$campCollaboration->inviteKey}/find");
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => 'Bi-Pi',
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
    public function testUserAlreadyInCampFalseForOwnCampCollaboration() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration6invitedWithUser'];

        /** @var User $invitedUser */
        $invitedUser = static::$fixtures['user6invited'];
        static::createClientWithCredentials(['email' => $invitedUser->getEmail()])
            ->request('GET', "/invitations/{$campCollaboration->inviteKey}/find")
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => $invitedUser->getDisplayName(),
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
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        static::createClientWithCredentials()->request('GET', "/invitations/{$campCollaboration->inviteKey}/find");
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => 'Bi-Pi',
            'userAlreadyInCamp' => true,
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    /**
     * A user should not accept a second invitation, because we cannot have 2 invitations with the same user attached.
     * Thus we tell him that there is already an invitation with his user, and he should use that one.
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testUserAlreadyInCampTrueWhenUserAlreadyInCampEvenIfInactive() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];

        /** @var User $inactiveUser */
        $inactiveUser = static::$fixtures['user5inactive'];
        static::createClientWithCredentials(['email' => $inactiveUser->getEmail()])
            ->request('GET', "/invitations/{$campCollaboration->inviteKey}/find")
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => $inactiveUser->getDisplayName(),
            'userAlreadyInCamp' => true,
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    public function testNotFoundWhenInvitekeyDoesNotMatch() {
        static::createBasicClient()->request('GET', '/invitations/notExisting/find');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testNotFoundWhenNoInviteKey() {
        static::createBasicClient()->request('GET', '/invitations/find');
        $this->assertResponseStatusCodeSame(404);
    }
}
