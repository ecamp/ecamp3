<?php

namespace App\Tests\Api\HttpCache;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\Period;
use App\HttpCache\CacheSecurityDelegateListener;
use App\Tests\Api\ECampApiTestCase;

/**
 * Responses that delegate their security to a camp are shared between everyone who can read the camp. Every operation delegating to a camp
 * has to be readable, with the same result, by everyone who can read the camp. The requests are built from the
 * metadata of the operations, so that a newly delegating operation is covered automatically.
 *
 * @internal
 */
class CacheSecurityDelegateOperationsTest extends ECampApiTestCase {
    private const PRIVATE_CAMP_FIXTURES = [
        Camp::class => 'camp1',
        Period::class => 'period1',
        Activity::class => 'activity1',
    ];

    private const PUBLIC_CAMP_FIXTURES = [
        Camp::class => 'campPrototype',
        Period::class => 'period1campPrototype',
        Activity::class => 'activity1campPrototype',
    ];

    public function testOperationsDelegatingToCampAreReadableWithTheSameResultByEveryCollaborator() {
        $guest = ['email' => static::$fixtures['user3guest']->getEmail()];

        foreach ($this->getCampDelegatedUrls(self::PRIVATE_CAMP_FIXTURES) as $operationName => $url) {
            $asManager = static::createClientWithCredentials()->request('GET', $url);
            $asGuest = static::createClientWithCredentials($guest)->request('GET', $url);

            $this->assertSame(200, $asManager->getStatusCode(), "{$operationName}: {$url} as manager");
            $this->assertSame(200, $asGuest->getStatusCode(), "{$operationName}: {$url} as guest");
            $this->assertSame($asManager->getContent(), $asGuest->getContent(), "{$operationName}: {$url} differs between manager and guest");
        }
    }

    public function testOperationsDelegatingToCampAreReadableByAnyoneIfTheCampIsPublic() {
        $unrelated = ['email' => static::$fixtures['user4unrelated']->getEmail()];

        foreach ($this->getCampDelegatedUrls(self::PUBLIC_CAMP_FIXTURES) as $operationName => $url) {
            $response = static::createClientWithCredentials($unrelated)->request('GET', $url);

            $this->assertSame(200, $response->getStatusCode(), "{$operationName}: {$url} as unrelated user");
        }
    }

    public function testPublicOperationsReturnTheSameResultForEveryone() {
        $unrelated = ['email' => static::$fixtures['user4unrelated']->getEmail()];

        foreach (['/', '/content_types'] as $url) {
            $asManager = static::createClientWithCredentials()->request('GET', $url);
            $asUnrelated = static::createClientWithCredentials($unrelated)->request('GET', $url);

            $this->assertSame($asManager->getContent(), $asUnrelated->getContent(), "{$url} differs between users");
        }
    }

    /**
     * @param array<class-string,string> $fixtures name of the fixture to use for the resources of the URL
     *
     * @return array<string,string> URL by operation name
     */
    private function getCampDelegatedUrls(array $fixtures): array {
        $urls = [];
        foreach ($this->getOperationsDelegatingToCamp() as $operation) {
            $url = str_replace('{._format}', '', $operation->getUriTemplate());

            foreach ($operation->getUriVariables() ?? [] as $name => $link) {
                $this->assertInstanceOf(Link::class, $link);
                $this->assertArrayHasKey($link->getFromClass(), $fixtures, "Add a fixture for {$link->getFromClass()} to test {$operation->getName()}.");

                $url = str_replace('{'.$name.'}', static::getFixture($fixtures[$link->getFromClass()])->getId(), $url);
            }

            $urls[$operation->getName()] = $url;
        }

        $this->assertNotEmpty($urls);

        return $urls;
    }

    /**
     * @return iterable<HttpOperation>
     */
    private function getOperationsDelegatingToCamp(): iterable {
        $names = static::getContainer()->get(ResourceNameCollectionFactoryInterface::class)->create();
        $factory = static::getContainer()->get(ResourceMetadataCollectionFactoryInterface::class);

        foreach ($names as $resourceClass) {
            foreach ($factory->create($resourceClass) as $resource) {
                foreach ($resource->getOperations() ?? [] as $operation) {
                    if (CacheSecurityDelegateListener::DELEGATE_CAMP === ($operation->getExtraProperties()[CacheSecurityDelegateListener::EXTRA_PROPERTY] ?? null)) {
                        yield $operation;
                    }
                }
            }
        }
    }
}
