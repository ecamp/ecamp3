<?php

namespace App\Tests\Api\SnapshotTests;

use App\Tests\Api\ECampApiTestCase;
use App\Tests\Spatie\Snapshots\Driver\ECampYamlSnapshotDriver;
use Hautelook\AliceBundle\PhpUnit\FixtureStore;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\greaterThanOrEqual;
use function PHPUnit\Framework\lessThanOrEqual;
use function PHPUnit\Framework\logicalAnd;

const MAX_EXECUTION_TIME_SECONDS = 0.5;

/**
 * @internal
 */
class EndpointPerformanceTest extends ECampApiTestCase {
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testPerformanceDidNotChangeForStableEndpoints() {
        $numberOfQueries = [];
        $responseCodes = [];
        $queryExecutionTime = [];
        $collectionEndpoints = self::getCollectionEndpoints();
        foreach ($collectionEndpoints as $collectionEndpoint) {
            if ('/users' !== $collectionEndpoint && !str_contains($collectionEndpoint, '/content_node')) {
                list($statusCode, $queryCount, $executionTimeSeconds) = $this->measurePerformanceFor($collectionEndpoint);
                $responseCodes[$collectionEndpoint] = $statusCode;
                $numberOfQueries[$collectionEndpoint] = $queryCount;
                $queryExecutionTime[$collectionEndpoint] = $executionTimeSeconds;
            }

            if (!str_contains($collectionEndpoint, '/content_node')) {
                $fixtureFor = $this->getFixtureFor($collectionEndpoint);
                list($statusCode, $queryCount, $executionTimeSeconds) = $this->measurePerformanceFor("{$collectionEndpoint}/{$fixtureFor->getId()}");
                $responseCodes["{$collectionEndpoint}/item"] = $statusCode;
                $numberOfQueries["{$collectionEndpoint}/item"] = $queryCount;
                $queryExecutionTime["{$collectionEndpoint}/item"] = $executionTimeSeconds;
            }
        }

        foreach ($this->getPerformanceCriticalUrls() as $url => $id) {
            list($statusCode, $queryCount, $executionTimeSeconds) = $this->measurePerformanceFor($url.$id);
            $responseCodes[$url] = $statusCode;
            $numberOfQueries[$url] = $queryCount;
            $queryExecutionTime[$url] = $executionTimeSeconds;
        }

        $not200Responses = array_filter($responseCodes, fn ($value) => 200 != $value);
        assertThat($not200Responses, equalTo([]));

        if (static::isPerformanceTestDebugOutput()) {
            var_dump($queryExecutionTime);
        }

        $endpointsWithTooLongExecutionTime = array_filter($queryExecutionTime, fn ($value) => MAX_EXECUTION_TIME_SECONDS < $value);

        $this->assertMatchesSnapshot($numberOfQueries, new ECampYamlSnapshotDriver());
        if ([] !== $endpointsWithTooLongExecutionTime) {
            self::markTestSkipped('Some endpoints have too long execution time, were: '.implode(',', array_keys($endpointsWithTooLongExecutionTime)));
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('getContentNodeEndpoints')]
    public function testNumberOfQueriesDidNotChangeForContentNodeCollectionEndpoints(string $collectionEndpoint) {
        if ('test' !== $this->getEnvironment()) {
            self::markTestSkipped(__FUNCTION__.' is only run in test environment, not in '.$this->getEnvironment());
        }
        list($statusCode, $queryCount, $executionTimeSeconds) = $this->measurePerformanceFor($collectionEndpoint);

        assertThat($statusCode, equalTo(200));

        if (static::isPerformanceTestDebugOutput()) {
            echo "{$collectionEndpoint}: {$executionTimeSeconds}\n";
        }

        $queryCountRanges = self::getContentNodeEndpointQueryCountRanges()[$collectionEndpoint];
        assertThat(
            $queryCount,
            logicalAnd(
                greaterThanOrEqual($queryCountRanges[0]),
                lessThanOrEqual($queryCountRanges[1]),
            )
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('getContentNodeEndpoints')]
    public function testNumberOfQueriesDidNotChangeForContentNodeItemEndpoints(string $collectionEndpoint) {
        if ('test' !== $this->getEnvironment()) {
            self::markTestSkipped(__FUNCTION__.' is only run in test environment, not in '.$this->getEnvironment());
        }
        if ('/content_nodes' === $collectionEndpoint) {
            self::markTestSkipped("{$collectionEndpoint} does not support get item endpoint");
        }
        $fixtureFor = $this->getFixtureFor($collectionEndpoint);
        list($statusCode, $queryCount, $executionTimeSeconds) = $this->measurePerformanceFor("{$collectionEndpoint}/{$fixtureFor->getId()}");

        assertThat($statusCode, equalTo(200));

        if (static::isPerformanceTestDebugOutput()) {
            echo "{$collectionEndpoint}: {$executionTimeSeconds}\n";
        }

        $queryCountRanges = self::getContentNodeEndpointQueryCountRanges()[$collectionEndpoint.'/item'];
        assertThat(
            $queryCount,
            logicalAnd(
                greaterThanOrEqual($queryCountRanges[0]),
                lessThanOrEqual($queryCountRanges[1]),
            )
        );

        if ($executionTimeSeconds > MAX_EXECUTION_TIME_SECONDS) {
            self::markTestSkipped("Endpoint {$collectionEndpoint} has too long execution time: {$executionTimeSeconds}");
        }
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
        // because measurements below 0.03 are flaky, we do not record
        // times below 0.03.
        $executionTimeSeconds = max(0.03, round($collector->getTime(), 2));

        return [$statusCode, $queryCount, $executionTimeSeconds];
    }

    public static function getContentNodeEndpoints(): array {
        $collectionEndpoints = self::getCollectionEndpoints();
        $normalEndpoints = array_filter($collectionEndpoints, function (string $endpoint) {
            return str_contains($endpoint, '/content_node');
        });

        // @noinspection PhpUnnecessaryLocalVariableInspection
        return array_reduce($normalEndpoints, function (?array $left, string $right) {
            $newArray = $left ?? [];
            $newArray[$right] = [$right];

            return $newArray;
        });
    }

    protected function getSnapshotId(): string {
        return $this->getEnvironment().'_'.parent::getSnapshotId();
    }

    private static function getContentNodeEndpointQueryCountRanges(): array {
        return [
            '/content_nodes' => [8, 11],
            '/content_node/column_layouts' => [6, 6],
            '/content_node/column_layouts/item' => [10, 10],
            '/content_node/checklist_nodes' => [6, 7],
            '/content_node/checklist_nodes/item' => [9, 9],
            '/content_node/material_nodes' => [6, 7],
            '/content_node/material_nodes/item' => [9, 9],
            '/content_node/multi_selects' => [6, 7],
            '/content_node/multi_selects/item' => [9, 9],
            '/content_node/responsive_layouts' => [6, 6],
            '/content_node/responsive_layouts/item' => [9, 9],
            '/content_node/single_texts' => [6, 7],
            '/content_node/single_texts/item' => [9, 9],
            '/content_node/storyboards' => [6, 7],
            '/content_node/storyboards/item' => [9, 9],
        ];
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    private static function getCollectionEndpoints() {
        static::bootKernel();
        $client = static::createClientWithCredentials();
        $response = $client->request('GET', '/');

        $responseArray = $response->toArray();
        $onlyUrls = array_map(fn (array $item) => $item['href'], $responseArray['_links']);
        $withoutParameters = array_map(fn (string $uriTemplate) => preg_replace('/\{[^}]*}/', '', $uriTemplate), $onlyUrls);
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
                '/auth/resend_activation' => false,
                '/invitations' => false,
                '/personal_invitations' => false,
                default => true
            };
        });

        return $normalEndpoints;
    }

    private function getPerformanceCriticalUrls(): array {
        return [
            '/activities?camp=' => urlencode($this->getIriFor('camp1')),
            '/activity_progress_labels?camp=' => urlencode($this->getIriFor('camp1')),
            '/activity_responsibles?activity.camp=' => urlencode($this->getIriFor('camp1')),
            '/camp_collaborations?camp=' => urlencode($this->getIriFor('camp1')),
            '/camp_collaborations?activityResponsibles.activity=' => urlencode($this->getIriFor('activity1')),
            '/categories?camp=' => urlencode($this->getIriFor('camp1')),
            '/content_types?categories=' => urlencode($this->getIriFor('category1')),
            '/day_responsibles?day.period=' => urlencode($this->getIriFor('period1')),
            '/material_items?materialList=' => urlencode($this->getIriFor('materialList1')),
            '/material_items?materialNode=' => urlencode($this->getIriFor('materialNode1')),
            '/material_items?period=' => urlencode($this->getIriFor('period1')),
            '/material_lists?camp=' => urlencode($this->getIriFor('camp1')),
            '/profiles?user.collaboration.camp=' => urlencode($this->getIriFor('camp1')),
            '/schedule_entries?period=' => urlencode($this->getIriFor('period1')),
        ];
    }

    private function getFixtureFor(string $collectionEndpoint) {
        $fixtures = FixtureStore::getFixtures();

        return ReadItemFixtureMap::get($collectionEndpoint, $fixtures);
    }

    private function getEnvironment(): string {
        return static::$kernel->getContainer()->getParameter('kernel.environment');
    }

    private static function isPerformanceTestDebugOutput(): bool {
        return 'true' === getenv('PERFORMANCE_TEST_DEBUG_OUTPUT');
    }
}
