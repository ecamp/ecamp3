<?php

namespace App\Tests\Api\SnapshotTests;

use App\Tests\Api\ECampApiTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\isEmpty;

/**
 * @internal
 */
class EndpointQueryCountTest extends ECampApiTestCase {
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testNumberOfQueriesDidNotChange() {
        $numberOfQueries = [];
        $responseCodes = [];
        $collectionEndpoints = $this->getCollectionEndpoints();
        foreach ($collectionEndpoints as $collectionEndpoint) {
            if ('/users' !== $collectionEndpoint) {
                list($statusCode, $queryCount) = $this->measurePerformanceFor($collectionEndpoint);
                $responseCodes[$collectionEndpoint] = $statusCode;
                $numberOfQueries[$collectionEndpoint] = $queryCount;
            }

            if ('/content_nodes' !== $collectionEndpoint) {
                $fixtureFor = $this->getFixtureFor($collectionEndpoint);
                list($statusCode, $queryCount) = $this->measurePerformanceFor("{$collectionEndpoint}/{$fixtureFor->getId()}");
                $responseCodes["{$collectionEndpoint}/item"] = $statusCode;
                $numberOfQueries["{$collectionEndpoint}/item"] = $queryCount;
            }
        }

        $not200Responses = array_filter($responseCodes, fn ($value) => 200 != $value);
        assertThat($not200Responses, isEmpty());

        $this->assertMatchesSnapshot($numberOfQueries);
    }

    /**
     * @param mixed $collectionEndpoint
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function measurePerformanceFor(string $collectionEndpoint): array {
        $client = $this->createClientWithCredentials();
        $client->enableProfiler();
        $response = $client->request('GET', $collectionEndpoint);
        $statusCode = $response->getStatusCode();
        $collector = $client->getProfile()->getCollector('db');
        $queryCount = $collector->getQueryCount();

        return [$statusCode, $queryCount];
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    private function getCollectionEndpoints() {
        static::bootKernel();
        $client = static::createClientWithCredentials();
        $response = $client->request('GET', '/');

        $responseArray = $response->toArray();
        $onlyUrls = array_map(fn (array $item) => $item['href'], $responseArray['_links']);
        $withoutParameters = array_map(fn (string $uriTemplate) => preg_replace('/\\{[^}]*}/', '', $uriTemplate), $onlyUrls);
        $normalEndpoints = array_filter($withoutParameters, function (string $endpoint) {
            // @noinspection PhpDuplicateMatchArmBodyInspection
            return match ($endpoint) {
                '/' => false,
                '/authentication_token' => false,
                '/auth/google' => false,
                '/auth/pbsmidata' => false,
                '/auth/cevidb' => false,
                '/auth/jubladb' => false,
                '/auth/reset_password' => false,
                '/invitations' => false,
                default => true
            };
        });

        return $normalEndpoints;
    }

    private function getFixtureFor(string $collectionEndpoint) {
        $fixtures = static::$fixtures;

        return ReadItemFixtureMap::get($collectionEndpoint, $fixtures);
    }
}
