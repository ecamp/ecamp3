<?php

namespace App\Tests\Api\Mercure;

use App\Entity\Period;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class MercureTokenTest extends ECampApiTestCase {
    public function testTokenIsDeniedForAnonymousUser() {
        $topic = $this->getIriFor('scheduleEntry1');
        static::createBasicClient()->request('POST', '/mercure/token', ['json' => ['topic' => $topic]]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testTokenIsIssuedForScheduleEntryToCampMember() {
        $topic = $this->getIriFor('scheduleEntry1');
        $response = static::createClientWithCredentials()->request('POST', '/mercure/token', ['json' => ['topic' => $topic]]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString('private', $response->getHeaders()['cache-control'][0] ?? '');
        $claims = $this->decodeClaims($response->toArray()['token']);
        $this->assertSame([['actions' => ['subscribe'], 'topics' => [$topic]]], $this->grantsOf($claims));
        $this->assertGreaterThan(time(), $claims['exp']);
        $this->assertLessThanOrEqual(time() + 300, $claims['exp']);
    }

    public function testTokenIsIssuedForScheduleEntryToGuest() {
        $topic = $this->getIriFor('scheduleEntry1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/mercure/token', ['json' => ['topic' => $topic]])
        ;

        $this->assertResponseStatusCodeSame(200);
    }

    public function testTokenIsIssuedForPeriodScheduleEntriesCollection() {
        /** @var Period $period */
        $period = static::getFixture('period1');
        $topic = '/periods/'.$period->getId().'/schedule_entries';
        $response = static::createClientWithCredentials()->request('POST', '/mercure/token', ['json' => ['topic' => $topic]]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(
            [['actions' => ['subscribe'], 'topics' => [$topic]]],
            $this->grantsOf($this->decodeClaims($response->toArray()['token']))
        );
    }

    public function testTokenIsNotFoundForScheduleEntryHiddenFromUnrelatedUser() {
        $topic = $this->getIriFor('scheduleEntry1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/mercure/token', ['json' => ['topic' => $topic]])
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTokenIsNotFoundForScheduleEntryHiddenFromInactiveCollaborator() {
        $topic = $this->getIriFor('scheduleEntry1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/mercure/token', ['json' => ['topic' => $topic]])
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTokenIsDeniedForPeriodScheduleEntriesCollectionOfUnrelatedUser() {
        /** @var Period $period */
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/mercure/token', ['json' => ['topic' => '/periods/'.$period->getId().'/schedule_entries']])
        ;

        $this->assertResponseStatusCodeSame(403);
    }

    public function testTokenIsNotFoundForUnknownResource() {
        static::createClientWithCredentials()
            ->request('POST', '/mercure/token', ['json' => ['topic' => '/schedule_entries/doesNotExist']])
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTokenIsNotFoundForPathWithoutResource() {
        static::createClientWithCredentials()
            ->request('POST', '/mercure/token', ['json' => ['topic' => '/this/is/not/a/resource']])
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTokenIsNotFoundForTopicWithQueryString() {
        /** @var Period $period */
        $period = static::getFixture('period1');
        static::createClientWithCredentials()
            ->request('POST', '/mercure/token', ['json' => ['topic' => '/schedule_entries?period=%2Fperiods%2F'.$period->getId()]])
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTokenIsNotFoundForNonGetOperation() {
        static::createClientWithCredentials()
            ->request('POST', '/mercure/token', ['json' => ['topic' => '/authentication_token']])
        ;

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTokenRequiresTopic() {
        static::createClientWithCredentials()->request('POST', '/mercure/token', ['json' => []]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testTokenRequiresStringTopic() {
        static::createClientWithCredentials()->request('POST', '/mercure/token', ['json' => ['topic' => ['/schedule_entries']]]);

        $this->assertResponseStatusCodeSame(400);
    }

    private function decodeClaims(string $jwt): array {
        $payload = explode('.', $jwt)[1];

        return json_decode(base64_decode(strtr($payload, '-_', '+/')), true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Returns the subscribe grants of a Mercure protocol 1.0 token as a list of [actions, matched topics].
     */
    private function grantsOf(array $claims): array {
        return array_map(
            fn (array $detail) => ['actions' => $detail['actions'], 'topics' => array_column($detail['topics'], 'match')],
            $claims['authorization_details'] ?? []
        );
    }
}
