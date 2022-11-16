<?php

namespace App\Tests\Api\Invitations;

use App\DTO\Invitation;
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
class AcceptInvitationTest extends ECampApiTestCase {
    /**
     * @throws TransportExceptionInterface
     */
    public function testAcceptInvitationFailsWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration2invitedCampUnrelated'];
        static::createBasicClient()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testAcceptInvitationSuccess() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration2invitedCampUnrelated'];
        static::createClientWithCredentials()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(200); // TO DISCUSS: Wouldn't it be better to get a 204 here? The invitation doesn't really exist anymore after successful acceptance
        $this->assertJsonContains([
            /*
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => 'Bi-Pi',
            'userAlreadyInCamp' => false, */
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCannotFindInvitationAfterSuccessfulAccept() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration2invitedCampUnrelated'];
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $client->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            /*
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
            'userDisplayName' => 'Bi-Pi',
            'userAlreadyInCamp' => false, */
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);

        $client->request('GET', "/invitations/{$campCollaboration->inviteKey}/find");
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testAcceptInvitationFailsWithExtraAttribute() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration2invitedCampUnrelated'];
        static::createClientWithCredentials()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT,
            [
                'json' => [
                    'userAlreadyInCamp' => true,
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
    public function testAcceptInvitationFailsWhenUserAlreadyInCamp() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        static::createClientWithCredentials()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT,
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
    public function testAcceptInvitationFailsWhenUserAlreadyInCampAndUserIsAttachedToInvitation() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration6invitedWithUser'];
        static::createClientWithCredentials()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * @dataProvider invalidMethods
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testInvalidRequestWhenWrongMethod(string $method) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration2invitedCampUnrelated'];
        static::createClientWithCredentials()->request($method, "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT);
        $this->assertResponseStatusCodeSame(405);
    }

    public static function invalidMethods(): array {
        return ['GET' => ['GET'], 'PUT' => ['PUT'], 'POST' => ['POST'], 'DELETE' => ['DELETE'], 'OPTIONS' => ['OPTIONS']];
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testNotFoundWhenInviteKeyDoesNotMatch() {
        static::createClientWithCredentials()->request('PATCH', '/invitations/notExisting/'.Invitation::ACCEPT);
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testNotFoundWhenNoInviteKey() {
        static::createClientWithCredentials()->request('PATCH', '/invitations/'.Invitation::ACCEPT);
        $this->assertResponseStatusCodeSame(405);
    }
}
