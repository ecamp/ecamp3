<?php

namespace App\Tests\Api\SnapshotTests;

use App\Entity\BaseEntity;
use App\Tests\Api\ECampApiTestCase;
use App\Tests\Constraints\CompatibleHalResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function PHPUnit\Framework\assertThat;

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
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @dataProvider getCollectionEndpoints
     */
    public function testGetCollectionMatchesStructure(string $endpoint) {
        $response = static::createClientWithCredentials()->request('GET', $endpoint);

        $this->assertResponseStatusCodeSame(200);
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
        $response = static::createClientWithCredentials()->request('GET', '/');

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
                '/users' => false,
                default => true
            };
        });

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $withUrlAsKey = array_reduce($normalEndpoints, function (?array $left, string $right) {
            $newArray = $left ?? [];
            $newArray[$right] = [$right];

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
     *
     * @dataProvider getItemEndpoints
     */
    public function testGetItemMatchesStructure(string $endpoint) {
        /** @var BaseEntity $fixtureFor */
        $fixtureFor = $this->getFixtureFor($endpoint);

        $itemResponse = static::createClientWithCredentials()->request('GET', "{$endpoint}/{$fixtureFor->getId()}");

        $this->assertResponseStatusCodeSame(200);
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
            return match ($endpoint[0]) {
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
     *
     * @dataProvider getPatchEndpoints
     */
    public function testPatchResponseMatchesGetItemResponse(string $endpoint) {
        /** @var BaseEntity $fixtureFor */
        $fixtureFor = $this->getFixtureFor($endpoint);

        $itemResponse = static::createClientWithCredentials()->request('GET', "{$endpoint}/{$fixtureFor->getId()}");
        $this->assertResponseStatusCodeSame(200);

        $patchResponse = static::createClientWithCredentials()
            ->request(
                'PATCH',
                "{$endpoint}/{$fixtureFor->getId()}",
                [
                    'json' => [],
                    'headers' => [
                        'Content-Type' => 'application/merge-patch+json',
                    ],
                ]
            )
        ;
        $this->assertResponseStatusCodeSame(200);

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
            return match ($endpoint[0]) {
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
        $fixtures = static::$fixtures;

        return match ($collectionEndpoint) {
            '/activities' => $fixtures['activity1'],
            '/activity_progress_labels' => $fixtures['activityProgressLabel1'],
            '/activity_responsibles' => $fixtures['activityResponsible1'],
            '/camp_collaborations' => $fixtures['campCollaboration1manager'],
            '/camps' => $fixtures['camp1'],
            '/categories' => $fixtures['category1'],
            '/content_node/column_layouts' => $fixtures['columnLayout2'],
            '/content_types' => $fixtures['contentTypeSafetyConcept'],
            '/day_responsibles' => $fixtures['dayResponsible1'],
            '/days' => $fixtures['day1period1'],
            '/material_items' => $fixtures['materialItem1'],
            '/material_lists' => $fixtures['materialList1'],
            '/content_node/material_nodes' => $fixtures['materialNode2'],
            '/content_node/multi_selects' => $fixtures['multiSelect1'],
            '/periods' => $fixtures['period1'],
            '/profiles' => $fixtures['profile1manager'],
            '/schedule_entries' => $fixtures['scheduleEntry1period1camp1'],
            '/content_node/single_texts' => $fixtures['singleText1'],
            '/content_node/storyboards' => $fixtures['storyboard1'],
            default => throw new \RuntimeException("no fixture defined for endpoint {$collectionEndpoint}")
        };
    }
}