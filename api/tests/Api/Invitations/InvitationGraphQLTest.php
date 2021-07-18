<?php

namespace App\Tests\Api\Invitations;

use App\Entity\CampCollaboration;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class InvitationGraphQLTest extends ECampApiTestCase {
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testFindInvitationWhenNotLoggedIn() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::$fixtures['campCollaboration1invited'];
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
