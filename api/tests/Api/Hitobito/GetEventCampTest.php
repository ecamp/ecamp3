<?php

namespace App\Tests\Api\Hitobito;

use App\Service\Hitobito\MockClient;

/**
 * @internal
 */
class GetEventCampTest extends HitobitoTestCase {
    public function testGetEventCampIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/camp');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetEventCampReturns404ForUnknownProvider() {
        static::createClientWithCredentials()->request('GET', '/hitobito/unknownprovider/events/'.MockClient::EVENT_ID_LEADER.'/camp');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetEventCampReturns403WithoutAccessTokenCookie() {
        static::createClientWithCredentials()->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/camp');

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains(['type' => '/errors/hitobito-access-token-invalid']);
    }

    public function testGetEventCampFailureCampForbidden() {
        $this->linkCampToEvent('campUnrelated');

        static::createClientWithCredentials()->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/camp');

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains(['type' => '/errors/hitobito-camp-forbidden']);
    }

    public function testGetEventCampFailureEventForbidden() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_COLEADER.'/camp');

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains(['type' => '/errors/hitobito-event-forbidden']);
    }

    public function testGetEventCampFailureEventExistsCampNotFound() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/camp');

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains(['type' => '/errors/hitobito-camp-not-found']);
    }

    public function testGetEventCampFailureEventNotFound() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/789/camp');

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains(['type' => '/errors/hitobito-event-not-found']);
    }

    public function testGetEventCampSuccess() {
        $camp = $this->linkCampToEvent('camp1');

        static::createClientWithCredentials()->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/camp');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'self' => ['href' => '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER.'/camp'],
                'camp' => ['href' => '/camps/'.$camp->getId()],
            ],
            'id' => (int) MockClient::EVENT_ID_LEADER,
        ]);
    }
}
