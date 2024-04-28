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

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\lessThanOrEqual;

/**
 * @internal
 */
class AcceptInvitationTest extends ECampApiTestCase {
    /**
     * @throws TransportExceptionInterface
     */
    public function testAcceptInvitationFailsWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
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
     * @throws TransportExceptionInterface
     */
    public function testAcceptInvitationDoesNotHitDBWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
        $client = static::createBasicClient();
        $client->enableProfiler();
        $client->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $collector = $client->getProfile()->getCollector('db');
        /*
         * 3 is:
         * BEGIN TRANSACTION
         * SAVEPOINT
         * RELEASE SAVEPOINT
         */
        assertThat($collector->getQueryCount(), lessThanOrEqual(3));
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
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
        static::createClientWithCredentials()->request(
            'PATCH',
            "/invitations/{$campCollaboration->inviteKey}/".Invitation::ACCEPT,
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
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCannotFindInvitationAfterSuccessfulAccept() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
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
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
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
        $campCollaboration = static::getFixture('campCollaboration4invited');
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
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
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
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('invalidMethods')]
    public function testInvalidRequestWhenWrongMethod(string $method) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');
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
        static::createClientWithCredentials()->request(
            'PATCH',
            '/invitations/notExisting/'.Invitation::ACCEPT,
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
            '/invitations/'.Invitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }
}
