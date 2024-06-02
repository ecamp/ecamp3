<?php

namespace App\Tests\Api\SnapshotTests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\BaseEntity;
use App\Tests\Api\ECampApiTestCase;
use App\Tests\Constraints\CompatibleHalResponse;
use App\Tests\Spatie\Snapshots\Driver\ECampYamlSnapshotDriver;
use App\Util\ArrayDeepSort;
use Hautelook\AliceBundle\PhpUnit\FixtureStore;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

/**
 * @internal
 */
class ResponseSnapshotTest extends ECampApiTestCase {
    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testRootEndpointMatchesSnapshot() {
        $response = static::createClientWithCredentials()->request('GET', '/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertMatchesResponseSnapshot($response);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testOpenApiSpecMatchesSnapshot() {
        $response = static::createClientWithCredentials()
            ->request(
                'GET',
                '/docs.jsonopenapi',
                [
                    'headers' => [
                        'accept' => 'application/vnd.openapi+json',
                    ],
                ]
            )
        ;

        $sortedOpenApiArray = ArrayDeepSort::sort($response->toArray());
        // Arguments for Yaml::dump taken from https://github.com/api-platform/core/blob/49c81194a3e6833f10d135c739776636775b15a5/src/OpenApi/Command/OpenApiCommand.php#L58
        $openApiYaml = Yaml::dump(
            input: $sortedOpenApiArray,
            inline: 10,
            indent: 2,
            flags: Yaml::DUMP_OBJECT_AS_MAP
            | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE
            | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
        );

        $this->assertMatchesSnapshot($openApiYaml, new ECampYamlSnapshotDriver());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('getCollectionEndpoints')]
    public function testGetCollectionMatchesStructure(Client $client, string $endpoint) {
        $response = $client->request('GET', $endpoint);

        assertThat($response->getStatusCode(), equalTo(200));
        $this->assertMatchesEscapedResponseSnapshot($response);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public static function getCollectionEndpoints() {
        static::bootKernel();
        $client = static::createClientWithCredentials();
        $client->disableReboot();
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
                '/auth/resend_activation' => false,
                '/invitations' => false,
                '/personal_invitations' => false,
                '/users' => false,
                default => true
            };
        });

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $withUrlAsKey = array_reduce($normalEndpoints, function (?array $left, string $right) use ($client) {
            $newArray = $left ?? [];
            $newArray[$right] = [$client, $right];

            return $newArray;
        });

        return $withUrlAsKey;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('getItemEndpoints')]
    public function testGetItemMatchesStructure(Client $client, string $endpoint) {
        /** @var BaseEntity $fixtureFor */
        $fixtureFor = $this->getFixtureFor($endpoint);

        $itemResponse = $client->request('GET', "{$endpoint}/{$fixtureFor->getId()}");

        assertThat($itemResponse->getStatusCode(), equalTo(200));
        $this->assertMatchesEscapedResponseSnapshot($itemResponse);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function getItemEndpoints() {
        return array_filter(self::getCollectionEndpoints(), function (array $endpoint) {
            return match ($endpoint[1]) {
                '/content_nodes' => false,
                default => true,
            };
        });
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[DataProvider('getPatchEndpoints')]
    public function testPatchResponseMatchesGetItemResponse(Client $client, string $endpoint) {
        /** @var BaseEntity $fixtureFor */
        $fixtureFor = $this->getFixtureFor($endpoint);

        $itemResponse = $client->request('GET', "{$endpoint}/{$fixtureFor->getId()}");
        assertThat($itemResponse->getStatusCode(), equalTo(200));

        $patchResponse = $client->request(
            'PATCH',
            "{$endpoint}/{$fixtureFor->getId()}",
            [
                'json' => [],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]
        );
        assertThat($patchResponse->getStatusCode(), equalTo(200));

        assertThat($itemResponse->toArray(), CompatibleHalResponse::isHalCompatibleWith($patchResponse->toArray()));
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function getPatchEndpoints() {
        return array_filter(self::getItemEndpoints(), function (array $endpoint) {
            return match ($endpoint[1]) {
                '/activity_responsibles' => false,
                // column layout has a problem with an empty patch
                '/content_node/column_layouts' => false,
                '/content_types' => false,
                '/days' => false,
                '/day_responsibles' => false,
                default => true,
            };
        });
    }

    private function getFixtureFor(string $collectionEndpoint) {
        $fixtures = FixtureStore::getFixtures();

        return ReadItemFixtureMap::get($collectionEndpoint, $fixtures);
    }
}
