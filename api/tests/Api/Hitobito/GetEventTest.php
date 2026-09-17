<?php

namespace App\Tests\Api\Hitobito;

use App\Service\Hitobito\MockClient;

/**
 * @internal
 */
class GetEventTest extends HitobitoTestCase {
    public function testGetEventIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/hitobito/pbsmidata/events/123');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetEventReturns404ForUnknownProvider() {
        static::createClientWithCredentials()->request('GET', '/hitobito/unknownprovider/events/'.MockClient::EVENT_ID_LEADER);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetEventReturns403WithoutAccessTokenCookie() {
        static::createClientWithCredentials()->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains(['type' => '/errors/hitobito-access-token-invalid']);
    }

    public function testGetEventReturns404ForUnknownEvent() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetEventReturns403WhenUserIsNotLeader() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_COLEADER);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetEventReturnsFullDetailsWhenUserIsLeader() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => 123,
            'name' => 'Testlager',
            'isImported' => false,
            'motto' => 'Testmotto',
            'location' => 'Testort',
            'dates' => [
                [
                    'id' => 789,
                    'label' => 'Hauptlager',
                    'startAt' => '2026-01-01T00:00:00+00:00',
                    'finishAt' => '2026-02-01T00:00:00+00:00',
                ],
            ],
        ]);
    }

    public function testGetEventMarksEventWithAnExistingCampAsImported() {
        $this->linkCampToEvent('camp1');

        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['isImported' => true]);
    }

    public function testGetEventMarksEventAsImportedEvenIfUserHasNoAccessToTheCamp() {
        $this->linkCampToEvent('campUnrelated');

        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('GET', '/hitobito/pbsmidata/events/'.MockClient::EVENT_ID_LEADER);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['isImported' => true]);
    }
}
