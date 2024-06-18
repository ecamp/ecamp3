<?php

namespace App\Tests\Api;

use App\Util\ParametrizedTestHelper;
use Doctrine\Bundle\DoctrineBundle\DataCollector\DoctrineDataCollector;
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
    #[DataProvider('getProtectedEndpoints')]
    public function testProtectedEndpointsDontResultInQuery(string $endpoint) {
        $client = self::createBasicClient();
        $client->enableProfiler();
        $client->request('GET', $endpoint);

        /**
         * @var DoctrineDataCollector
         */
        $collector = $client->getProfile()->getCollector('db');
        /*
         * 3 is:
         * BEGIN TRANSACTION
         * SAVEPOINT
         * RELEASE SAVEPOINT
         */
        assertThat($collector->getQueryCount(), equalTo(3));
    }

    public static function getProtectedEndpoints(): array {
        $protectedEndpoints = array_filter(self::getEndPoints(), function (string $endpoint) {
            return self::isProtectedByFirewall($endpoint);
        });

        return ParametrizedTestHelper::asParameterTestSets($protectedEndpoints);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('getUnprotectedEndpoints')]
    public function testUnprotectedEndpointsMayResultInQuery(string $endpoint) {
        $client = self::createBasicClient();
        $client->enableProfiler();
        $client->request('GET', $endpoint);

        /**
         * @var DoctrineDataCollector
         */
        $collector = $client->getProfile()->getCollector('db');
        /*
         * 3 is:
         * BEGIN TRANSACTION
         * SAVEPOINT
         * RELEASE SAVEPOINT
         */
        assertThat($collector->getQueryCount(), greaterThanOrEqual(3));
    }

    public static function getUnprotectedEndpoints() {
        $protectedEndpoints = array_filter(self::getEndPoints(), function (string $endpoint) {
            return !self::isProtectedByFirewall($endpoint);
        });

        return ParametrizedTestHelper::asParameterTestSets($protectedEndpoints);
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
