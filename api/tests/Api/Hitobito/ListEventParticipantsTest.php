<?php

namespace App\Tests\Api\Hitobito;

use App\Service\Hitobito\MockClient;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListEventParticipantsTest extends ECampApiTestCase {
    public function testListEventParticipantsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/participants');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListEventParticipantsReturns404ForUnknownProvider() {
        static::createClientWithCredentials()->request('GET', '/hitobito/unknownprovider/events/'.MockClient::EVENT_ID_LEADER.'/participants');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testListEventParticipantsReturns403WithoutAccessTokenCookie() {
        static::createClientWithCredentials()->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/participants');

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains(['type' => '/errors/hitobito-access-token-invalid']);
    }

    public function testListEventParticipantsReturns404ForUnknownEvent() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/999/participants');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testListEventParticipantsReturns403WhenUserIsNotLeader() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_COLEADER.'/participants');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testListEventParticipantsReturnsParticipantsWhenUserIsLeader() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/participants');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 2,
            '_embedded' => [
                'items' => [
                    [
                        'firstName' => 'Ellen',
                        'lastName' => 'Bloch',
                        'nickname' => 'Quo',
                        'email' => 'bloch.ellen@hitobito.example.com',
                    ],
                    [
                        'firstName' => 'Lee',
                        'lastName' => 'Frauen',
                        'nickname' => 'Maiores',
                        'email' => 'frauen_lee@hitobito.example.com',
                    ],
                ],
            ],
        ]);
    }
}
