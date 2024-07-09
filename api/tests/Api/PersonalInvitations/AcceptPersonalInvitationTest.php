<?php

namespace App\Tests\Api\PersonalInvitations;

use App\DTO\PersonalInvitation;
use App\Entity\CampCollaboration;
use App\Entity\Profile;
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
class AcceptPersonalInvitationTest extends ECampApiTestCase {
    /**
     * @throws TransportExceptionInterface
     */
    public function testAcceptPersonalInvitationFailsWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        static::createBasicClient()->request(
            'PATCH',
            "/personal_invitations/{$campCollaboration->getId()}/".PersonalInvitation::ACCEPT,
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
    public function testAcceptPersonalInvitationDoesNotHitDBWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        $client = static::createBasicClient();
        $client->enableProfiler();
        $client->request(
            'PATCH',
            "/personal_invitations/{$campCollaboration->getId()}/".PersonalInvitation::ACCEPT,
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
    public function testAcceptPersonalInvitationSuccess() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');

        /** @var Profile $profile */
        $profile = static::getFixture('profile6invited');
        static::createClientWithCredentials(['email' => $profile->email])->request(
            'PATCH',
            "/personal_invitations/{$campCollaboration->getId()}/".PersonalInvitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'self' => ['href' => "/personal_invitations/{$campCollaboration->getId()}"],
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
    public function testCannotFindPersonalInvitationAfterSuccessfulAccept() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');

        /** @var Profile $profile */
        $profile = static::getFixture('profile6invited');
        $client = static::createClientWithCredentials(['email' => $profile->email]);
        $client->disableReboot();
        $client->request(
            'PATCH',
            "/personal_invitations/{$campCollaboration->getId()}/".PersonalInvitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'self' => ['href' => "/personal_invitations/{$campCollaboration->getId()}"],
            ],
        ]);

        $client->request('GET', "/personal_invitations/{$campCollaboration->getId()}");
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testAcceptPersonalInvitationFailsWithExtraAttribute() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');

        /** @var Profile $profile */
        $profile = static::getFixture('profile6invited');
        static::createClientWithCredentials(['email' => $profile->email])->request(
            'PATCH',
            "/personal_invitations/{$campCollaboration->getId()}/".PersonalInvitation::ACCEPT,
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
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('invalidMethods')]
    public function testInvalidRequestWhenWrongMethod(string $method) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        static::createClientWithCredentials()->request($method, "/personal_invitations/{$campCollaboration->getId()}/".PersonalInvitation::ACCEPT);
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
    public function testNotFoundWhenIdDoesNotMatch() {
        /** @var Profile $profile */
        $profile = static::getFixture('profile6invited');
        static::createClientWithCredentials(['email' => $profile->email])->request(
            'PATCH',
            '/personal_invitations/notExisting/'.PersonalInvitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testNotFoundWhenUserHasNoAccessToPersonalInvitation() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');

        /** @var Profile $profile */
        $profile = static::getFixture('profile8memberOnlyInCamp2');
        static::createClientWithCredentials(['email' => $profile->email])->request(
            'PATCH',
            "/personal_invitations/{$campCollaboration->getId()}/".PersonalInvitation::ACCEPT,
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
    public function testMethodNotAllowedWhenNoId() {
        /** @var Profile $profile */
        $profile = static::getFixture('profile6invited');
        static::createClientWithCredentials(['email' => $profile->email])->request(
            'PATCH',
            '/personal_invitations/'.PersonalInvitation::ACCEPT,
            [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );
        $this->assertResponseStatusCodeSame(405);
    }
}
