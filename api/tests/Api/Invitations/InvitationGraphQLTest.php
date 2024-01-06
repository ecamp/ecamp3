<?php

namespace App\Tests\Api\Invitations;

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
class InvitationGraphQLTest extends ECampApiTestCase {
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testFindInvitationWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration4invited'];
        $query = "
        {
          invitation(id: \"invitations/{$campCollaboration->inviteKey}/find\") {
            campTitle
            campId
            userDisplayName
            userAlreadyInCamp
          }
        }
        ";

        static::createClient()->request('GET', '/graphql?'.http_build_query(['query' => $query]));

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'data' => [
                'invitation' => [
                    'campId' => $campCollaboration->camp->getId(),
                    'campTitle' => $campCollaboration->camp->title,
                    'userDisplayName' => null,
                    'userAlreadyInCamp' => null,
                ],
            ],
        ]);
    }
}
