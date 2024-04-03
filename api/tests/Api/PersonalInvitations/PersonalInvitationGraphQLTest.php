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
class PersonalInvitationGraphQLTest extends ECampApiTestCase {
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testFindPersonalInvitationWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        $query = "
        {
          personalInvitation(id: \"personal_invitations/{$campCollaboration->getId()}\") {
            id
            campTitle
            campId
          }
        }
        ";

        static::createClient()->request('GET', '/graphql?'.http_build_query(['query' => $query]));

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testFindPersonalInvitationWhenLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration6invitedWithUser');
        $query = "
        {
          personalInvitation(id: \"personal_invitations/{$campCollaboration->getId()}\") {
            id
            campTitle
            campId
          }
        }
        ";

        /** @var Profile $profile */
        $profile = static::getFixture('profile6invited');
        static::createClientWithCredentials(['email' => $profile->email])->request('GET', '/graphql?'.http_build_query(['query' => $query]));

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'data' => [
                'personalInvitation' => [
                    'id' => '/personal_invitations/'.$campCollaboration->getId(),
                    'campId' => $campCollaboration->camp->getId(),
                    'campTitle' => $campCollaboration->camp->title,
                ],
            ],
        ]);
    }
}
