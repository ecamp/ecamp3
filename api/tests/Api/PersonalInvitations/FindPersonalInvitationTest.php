<?php

namespace App\Tests\Api\PersonalInvitations;

use App\Entity\CampCollaboration;
use App\Entity\Profile;
use App\Tests\Api\ECampApiTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @internal
 */
class FindPersonalInvitationTest extends ECampApiTestCase {
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testDoesNotFindPersonalInvitationWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        static::createBasicClient()->request('GET', "/personal_invitations/{$campCollaboration->getId()}");
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testFindOwnPersonalInvitationWhenLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');

        /** @var Profile $profile */
        $profile = static::getFixture('profile6invited');
        static::createClientWithCredentials(['email' => $profile->email])->request('GET', "/personal_invitations/{$campCollaboration->getId()}");
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $campCollaboration->getId(),
            'campId' => $campCollaboration->camp->getId(),
            'campTitle' => $campCollaboration->camp->title,
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
    public function testDoesNotFindOtherPersonalInvitationWhenLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        static::createClientWithCredentials()->request('GET', "/personal_invitations/{$campCollaboration->getId()}");
        $this->assertResponseStatusCodeSame(404);
    }
}
