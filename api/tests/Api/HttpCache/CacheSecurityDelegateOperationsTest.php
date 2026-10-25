<?php

namespace App\Tests\Api\HttpCache;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Test\Client;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\Period;
use App\HttpCache\CacheSecurityDelegateListener;
use App\Tests\Api\ECampApiTestCase;

/**
 * Responses that delegate their security to a resource are shared between everyone who can read that resource.
 * Therefore everyone who can read the delegate has to be able to read the response, with the same result.
 *
 * The requests are built from the metadata of the operations, so that a newly delegating operation is covered
 * automatically. Operations are tested on a private and on a public camp, as different users can read them.
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

    public function testEveryoneWhoCanReadTheDelegateCanReadOperationsOfAPrivateCamp() {
        $readers = $this->assertReadableByEveryReaderOfTheDelegate(self::PRIVATE_CAMP_FIXTURES);

        $this->assertContains('user1manager', $readers);
        $this->assertContains('user3guest', $readers);
        $this->assertNotContains('user4unrelated', $readers);
    }

    public function testEveryoneWhoCanReadTheDelegateCanReadOperationsOfAPublicCamp() {
        $readers = $this->assertReadableByEveryReaderOfTheDelegate(self::PUBLIC_CAMP_FIXTURES);

        $this->assertContains('user4unrelated', $readers);
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
     * @param array<class-string,string> $fixtures name of the fixture to use for the resources of the URLs
     *
     * @return string[] names of the users who can read the delegate of every operation
     */
    private function assertReadableByEveryReaderOfTheDelegate(array $fixtures): array {
        $clients = $this->createClientsOfAllUsers();
        $readersOfAll = array_keys($clients);

        foreach ($this->getUrlsOfDelegatingOperations($fixtures) as $operationName => $url) {
            $responses = array_map(fn (Client $client) => $client->request('GET', $url), $clients);

            $delegate = $this->getDelegateOfFirstSuccessfulResponse($responses, "{$operationName}: {$url}");
            $readers = array_keys(array_filter($clients, fn (Client $client) => 200 === $client->request('GET', $delegate)->getStatusCode()));
            $readersOfAll = array_intersect($readersOfAll, $readers);

            $this->assertNotEmpty($readers, "{$operationName}: nobody can read the delegate {$delegate}");
            $expectedContent = $responses[$readers[0]]->getContent(false);
            foreach ($readers as $reader) {
                $this->assertSame(200, $responses[$reader]->getStatusCode(), "{$operationName}: {$url} is not readable by {$reader}, who can read the delegate {$delegate}");
                $this->assertSame($expectedContent, $responses[$reader]->getContent(false), "{$operationName}: {$url} differs between {$readers[0]} and {$reader}");
            }
        }

        return $readersOfAll;
    }

    /**
     * @return array<string,Client> by name of the user fixture
     */
    private function createClientsOfAllUsers(): array {
        $clients = [];
        foreach (static::$fixtures as $name => $fixture) {
            if (preg_match('/^user\d/', $name)) {
                $clients[$name] = static::createClientWithCredentials(['email' => $fixture->getEmail()]);
            }
        }

        return $clients;
    }

    private function getDelegateOfFirstSuccessfulResponse(array $responses, string $message): string {
        foreach ($responses as $response) {
            if (200 === $response->getStatusCode()) {
                $delegate = $response->getHeaders()[strtolower(CacheSecurityDelegateListener::HEADER)][0] ?? null;
                $this->assertNotNull($delegate, "{$message} has no delegate");

                return $delegate;
            }
        }

        $this->fail("{$message} is not readable by anyone");
    }

    /**
     * @param array<class-string,string> $fixtures
     *
     * @return array<string,string> URL by operation name
     */
    private function getUrlsOfDelegatingOperations(array $fixtures): array {
        $urls = [];
        foreach ($this->getOperationsDelegatingToAResource() as $operation) {
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
    private function getOperationsDelegatingToAResource(): iterable {
        $names = static::getContainer()->get(ResourceNameCollectionFactoryInterface::class)->create();
        $factory = static::getContainer()->get(ResourceMetadataCollectionFactoryInterface::class);

        foreach ($names as $resourceClass) {
            foreach ($factory->create($resourceClass) as $resource) {
                foreach ($resource->getOperations() ?? [] as $operation) {
                    $delegate = $operation->getExtraProperties()[CacheSecurityDelegateListener::EXTRA_PROPERTY] ?? null;
                    if (null !== $delegate && CacheSecurityDelegateListener::DELEGATE_PUBLIC !== $delegate) {
                        yield $operation;
                    }
                }
            }
        }
    }
}
