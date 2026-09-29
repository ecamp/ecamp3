<?php

namespace App\Tests\Api\Camps;

use ApiPlatform\Metadata\Post;
use App\Entity\Camp;
use App\Service\Hitobito\HitobitoProvider;
use App\Service\Hitobito\MockClient;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCampWithHitobitoEventTest extends ECampApiTestCase {
    public function testCreateCampLinksTheCampToTheHitobitoEvent() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
            'hitobitoEventId' => MockClient::EVENT_ID_LEADER,
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
            'hitobitoEventId' => MockClient::EVENT_ID_LEADER,
        ]);
    }

    public function testCreateCampValidatesHitobitoProviderWithoutEventId() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'hitobitoProvider',
                    'message' => 'hitobitoProvider and hitobitoEventId must be set together.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesHitobitoEventIdWithoutProvider() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'hitobitoEventId' => MockClient::EVENT_ID_LEADER,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'hitobitoProvider',
                    'message' => 'hitobitoProvider and hitobitoEventId must be set together.',
                ],
            ],
        ]);
    }

    public function testCreateCampValidatesUnsupportedHitobitoProvider() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'hitobitoProvider' => HitobitoProvider::CEVIDB->value,
            'hitobitoEventId' => MockClient::EVENT_ID_LEADER,
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'hitobitoProvider',
                    'message' => 'This Hitobito provider is not supported.',
                ],
            ],
        ]);
    }

    public function testCreateCampWithHitobitoEventRequiresAccessTokenCookie() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
            'hitobitoEventId' => MockClient::EVENT_ID_LEADER,
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains(['type' => '/errors/hitobito-access-token-invalid']);
    }

    public function testCreateCampWithUnknownHitobitoEvent() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
            'hitobitoEventId' => '999',
        ])]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateCampWithHitobitoEventWhereUserIsNotLeader() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
            'hitobitoEventId' => MockClient::EVENT_ID_COLEADER,
        ])]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateCampFromAlreadyLinkedHitobitoEvent() {
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        static::addHitobitoAccessTokenCookie($client);

        $payload = $this->getExampleWritePayload([
            'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
            'hitobitoEventId' => MockClient::EVENT_ID_LEADER,
        ]);

        $client->request('POST', '/camps', ['json' => $payload]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/camps', ['json' => $payload]);
        $this->assertResponseStatusCodeSame(409);
    }

    public function testCreateCampStoresTheHitobitoIdOfThePeriods() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $response = $client->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
            'hitobitoEventId' => MockClient::EVENT_ID_LEADER,
            'periods' => [
                [
                    'description' => 'Hauptlager',
                    'start' => '2026-01-01',
                    'end' => '2026-02-01',
                    'hitobitoId' => MockClient::EVENT_DATE_ID,
                ],
            ],
        ])]);

        $this->assertResponseStatusCodeSame(201);

        /** @var Camp $camp */
        $camp = $this->getEntityManager()->getRepository(Camp::class)->find($response->toArray()['id']);
        $this->assertSame(MockClient::EVENT_DATE_ID, $camp->periods[0]->hitobitoId);
    }

    public function testCreateCampValidatesPeriodHitobitoIdWithoutHitobitoEvent() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        $client->request('POST', '/camps', ['json' => $this->getExampleWritePayload([
            'periods' => [
                [
                    'description' => 'Hauptlager',
                    'start' => '2026-01-01',
                    'end' => '2026-02-01',
                    'hitobitoId' => MockClient::EVENT_DATE_ID,
                ],
            ],
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'periods[0].hitobitoId',
                    'message' => 'hitobitoId may only be set on periods of a camp that is linked to a Hitobito event.',
                ],
            ],
        ]);
    }

    public function testHitobitoFieldsAreNotWritableOnUpdate() {
        $client = static::createClientWithCredentials();
        static::addHitobitoAccessTokenCookie($client);

        /** @var Camp $camp */
        $camp = static::getFixture('camp1');
        $client->request('PATCH', '/camps/'.$camp->getId(), [
            'json' => [
                'hitobitoProvider' => HitobitoProvider::PBSMIDATA->value,
                'hitobitoEventId' => MockClient::EVENT_ID_LEADER,
            ],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("hitobitoProvider", "hitobitoEventId" are unknown).',
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Camp::class,
            Post::class,
            $attributes,
            ['campPrototype', 'hitobitoProvider', 'hitobitoEventId'],
            $except
        );
    }
}
