<?php

namespace App\Tests\Api\Invitations;

use App\DTO\Invitation;
use App\Entity\CampCollaboration;
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
class RejectInvitationTest extends ECampApiTestCase {
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testRejectInvitationSucceedsWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
        static::createBasicClient()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::REJECT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testCannotFindInvitationAfterSuccessfulReject() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
        $client = static::createBasicClient();
        $client->disableReboot();
        $client->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::REJECT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);

        $client->request('GET', "/invitations/{$campCollaboration->inviteKey}/find");
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testRejectInvitationSucceedsWhenLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
        static::createClientWithCredentials()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::REJECT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testRejectInvitationFailsWithExtraAttribute() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
        static::createClientWithCredentials()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::REJECT,
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
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testRejectInvitationSucceedsWhenUserAlreadyInCamp() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration4invited');
        static::createClientWithCredentials()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::REJECT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'self' => ['href' => "/invitations/{$campCollaboration->inviteKey}/find"],
            ],
        ]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('invalidMethods')]
    public function testInvalidRequestWhenWrongMethod(string $method) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
        static::createClientWithCredentials()->request($method, "/invitations/{$campCollaboration->inviteKey}/".Invitation::REJECT);
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
        static::createClientWithCredentials()->request(
            'PATCH',
            '/invitations/notExisting/'.Invitation::REJECT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testNotFoundWhenNoInviteKey() {
        static::createClientWithCredentials()->request(
            'PATCH',
            '/invitations/'.Invitation::REJECT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }
}
