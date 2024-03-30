<?php

namespace Api;

use App\Tests\Api\ECampApiTestCase;
use App\Util\ParametrizedTestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\greaterThanOrEqual;

/**
 * @internal
 */
class FirewallTest extends ECampApiTestCase {
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('getProtectedEnpoints')]
    public function testProtectedEndpointsDontResultInQuery(string $endpoint) {
        $client = self::createBasicClient();
        $client->enableProfiler();
        $client->request('GET', $endpoint);

        $collector = $client->getProfile()->getCollector('db');
        /*
         * 3 is:
         * BEGIN TRANSACTION
         * SAVEPOINT
         * RELEASE SAVEPOINT
         */
        assertThat($collector->getQueryCount(), equalTo(3));
    }

    public static function getProtectedEnpoints(): array {
        $protectedEnpoints = array_filter(self::getEndPoints(), function (string $endpoint) {
            return self::isProtectedByFirewall($endpoint);
        });

        return ParametrizedTestHelper::asParameterTestSets($protectedEnpoints);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('getUnprotectedEnpoints')]
    public function testUnprotectedEndpointsMayResultInQuery(string $endpoint) {
        $client = self::createBasicClient();
        $client->enableProfiler();
        $client->request('GET', $endpoint);

        $collector = $client->getProfile()->getCollector('db');
        /*
         * 3 is:
         * BEGIN TRANSACTION
         * SAVEPOINT
         * RELEASE SAVEPOINT
         */
        assertThat($collector->getQueryCount(), greaterThanOrEqual(3));
    }

    public static function getUnprotectedEnpoints() {
        $protectedEnpoints = array_filter(self::getEndPoints(), function (string $endpoint) {
            return !self::isProtectedByFirewall($endpoint);
        });

        return ParametrizedTestHelper::asParameterTestSets($protectedEnpoints);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    private static function getEndPoints() {
        static::bootKernel();
        $client = static::createBasicClient();
        $response = $client->request('GET', '/');

        $responseArray = $response->toArray();
        $onlyUrls = array_map(fn (array $item) => $item['href'], $responseArray['_links']);

        return array_map(fn (string $uriTemplate) => preg_replace('/\\{[^}]*}/', '', $uriTemplate), $onlyUrls);
    }

    private static function isProtectedByFirewall(mixed $endpoint): bool {
        return match ($endpoint) {
            '/authentication_token' => false,
            '/auth/google' => false,
            '/auth/pbsmidata' => false,
            '/auth/cevidb' => false,
            '/auth/jubladb' => false,
            '/auth/reset_password' => false,
            '/content_types' => false,
            '/invitations' => false,
            default => true
        };
    }
}
