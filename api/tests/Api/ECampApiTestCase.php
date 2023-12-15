<?php

namespace App\Tests\Api;

use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\BaseEntity;
use App\Entity\Profile;
use App\Entity\User;
use App\Metadata\Resource\OperationHelper;
use App\Repository\ProfileRepository;
use App\Util\ArrayDeepSort;
use Doctrine\Bundle\DoctrineBundle\DataCollector\DoctrineDataCollector;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class ECampApiTestCase extends ApiTestCase {
    use RefreshDatabaseTrait;
    use MatchesSnapshots;

    protected string $endpoint = '';
    protected string $routePrefix = '';
    protected BaseEntity $defaultEntity;
    protected string $entityClass;

    private ?string $token = null;
    private ?IriConverterInterface $iriConverter = null;
    private ?SchemaFactoryInterface $schemaFactory = null;
    private ?ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory = null;
    private ?EntityManagerInterface $entityManager = null;

    /** @var string */
    private $currentTimezone;

    public function setUp(): void {
        self::bootKernel();
        parent::setUp();

        // backup current timezone, in case it's change in one of the tests
        $this->currentTimezone = date_default_timezone_get();
    }

    protected function tearDown(): void {
        date_default_timezone_set($this->currentTimezone);

        parent::tearDown();
    }

    public static function escapeArrayValues(array $array): array {
        $clonedArray = self::deepCloneArray($array);
        array_walk_recursive($clonedArray, fn (mixed &$value) => self::escapeValues($value));

        return $clonedArray;
    }

    public static function deepCloneArray(array $array): mixed {
        $json_encode = json_encode($array);

        return json_decode($json_encode, true);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected static function createClientWithCredentials(?array $credentials = null, ?array $headers = null): Client {
        $client = static::createBasicClient($headers);

        /** @var Profile $profile */
        $profile = static::getContainer()->get(ProfileRepository::class)
            ->findOneBy(
                array_diff_key($credentials ?: ['email' => 'test@example.com'], ['password' => ''])
            )
        ;
        $user = $profile->user;

        $jwtToken = static::getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);
        $lastPeriodPosition = strrpos($jwtToken, '.');
        $jwtHeaderAndPayload = substr($jwtToken, 0, $lastPeriodPosition);
        $jwtSignature = substr($jwtToken, $lastPeriodPosition + 1);

        $cookies = $client->getCookieJar();
        $cookies->set(new Cookie('example_com_jwt_hp', $jwtHeaderAndPayload, null, null, 'localhost', false, false, false, 'strict'));
        $cookies->set(new Cookie('example_com_jwt_s', $jwtSignature, null, null, 'localhost', false, true, false, 'strict'));

        return $client;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected static function createClientWithAdminCredentials(?array $headers = null): Client {
        return static::createClientWithCredentials(['email' => 'admin@example.com']);
    }

    protected static function createBasicClient(?array $headers = null): Client {
        return static::createClient([], ['headers' => $headers ?: ['accept' => 'application/hal+json', 'content-type' => 'application/ld+json']]);
    }

    protected function getIriConverter(): IriConverterInterface {
        if (null === $this->iriConverter) {
            $this->iriConverter = static::getContainer()->get(IriConverterInterface::class);
        }

        return $this->iriConverter;
    }

    protected function getIriFor($entityOrFixtureName): string {
        if (is_string($entityOrFixtureName)) {
            // Assume we want to get the IRI for a fixture
            $entityOrFixtureName = static::$fixtures[$entityOrFixtureName];
        }

        return $this->getIriConverter()->getIriFromResource($entityOrFixtureName);
    }

    protected function getSchemaFactory(): SchemaFactoryInterface {
        if (null === $this->schemaFactory) {
            $this->schemaFactory = static::getContainer()->get(SchemaFactoryInterface::class);
        }

        return $this->schemaFactory;
    }

    protected function getResourceMetadataFactory(): ResourceMetadataCollectionFactoryInterface {
        if (null === $this->resourceMetadataFactory) {
            $this->resourceMetadataFactory = static::getContainer()->get(ResourceMetadataCollectionFactoryInterface::class);
        }

        return $this->resourceMetadataFactory;
    }

    protected function getEntityManager(): EntityManagerInterface {
        if (null === $this->entityManager) {
            $this->entityManager = static::getContainer()->get('doctrine.orm.default_entity_manager');
        }

        return $this->entityManager;
    }

    protected function getExamplePayload(string $resourceClass, string $operationClassName = Get::class, array $attributes = [], array $exceptExamples = [], array $exceptAttributes = []): array {
        $resourceMetadataCollection = $this->getResourceMetadataFactory()->create($resourceClass);
        $operation = OperationHelper::findOneByType($resourceMetadataCollection, $operationClassName);

        if (null === $operation) {
            throw new \RuntimeException("Requested operation of type {$operationClassName} on resource {$resourceClass} was not found in order to build example payload.");
        }

        try {
            // build JSON schema based on requested operation
            $schema = $this->getSchemaFactory()->buildSchema($resourceClass, 'json', in_array($operationClassName, [Get::class, GetCollection::class]) ? Schema::TYPE_OUTPUT : Schema::TYPE_INPUT, $operation);

            // transform schema into example payload
            preg_match('/\/([^\/]+)$/', $schema['$ref'] ?? '', $matches);
            $schemaName = $matches[1];
            $properties = $schema->getDefinitions()[$schemaName]['properties'] ?? [];
            $writableProperties = array_filter($properties, fn ($property) => !($property['readOnly'] ?? false));
            $writablePropertiesWithExample = array_filter($writableProperties, fn ($property) => ($property['example'] ?? false));
            $examples = array_map(fn ($property) => $property['example'] ?? $property['default'] ?? null, $writablePropertiesWithExample);
            $examples = array_map(function ($example) {
                try {
                    $decoded = json_decode($example, true, 512, JSON_THROW_ON_ERROR);

                    return is_array($decoded) || is_null($decoded) ? $decoded : $example;
                } catch (\JsonException|\TypeError $e) {
                    return $example;
                }
            }, $examples);

            return array_diff_key(array_merge(array_diff_key($examples, array_flip($exceptExamples)), $attributes), array_flip($exceptAttributes));
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function get(?BaseEntity $entity = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        $entity ??= $this->defaultEntity;

        return static::createClientWithCredentials($credentials)->request('GET', $this->endpoint.'/'.$entity->getId());
    }

    protected function list(?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        return static::createClientWithCredentials($credentials)->request('GET', $this->endpoint);
    }

    protected function delete(?BaseEntity $entity = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        $entity ??= $this->defaultEntity;

        return static::createClientWithCredentials($credentials)->request('DELETE', $this->endpoint.'/'.$entity->getId());
    }

    protected function create(array $payload = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        if (null === $payload) {
            $payload = $this->getExampleWritePayload();
        }

        return static::createClientWithCredentials($credentials)->request('POST', $this->endpoint, ['json' => $payload]);
    }

    protected function patch(?BaseEntity $entity = null, array $payload = [], ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        $entity ??= $this->defaultEntity;

        return static::createClientWithCredentials($credentials)->request('PATCH', $this->endpoint.'/'.$entity->getId(), [
            'json' => $payload,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
    }

    protected function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            $this->entityClass,
            Post::class,
            $attributes,
            [],
            $except
        );
    }

    protected function assertEntityWasRemoved(?BaseEntity $entity = null) {
        $entity ??= $this->defaultEntity;

        $this->assertNull($this->getEntityManager()->getRepository(get_class($entity))->find($entity->getId()));
    }

    protected function assertEntityStillExists(?BaseEntity $entity = null) {
        $entity ??= $this->defaultEntity;

        $this->assertNotNull($this->getEntityManager()->getRepository(get_class($entity))->find($entity->getId()));
    }

    /**
     * @param ResponseInterface $response Response from API
     * @param array             $items    array of IRIs
     */
    protected function assertJsonContainsItems(ResponseInterface $response, array $items = []) {
        $this->assertJsonContains([
            'totalItems' => count($items),
        ]);

        if (!empty($items)) {
            $this->assertJsonContains([
                '_links' => [
                    'items' => [],
                ],
                '_embedded' => [
                    'items' => [],
                ],
            ]);

            $this->assertEqualsCanonicalizing(
                array_map(fn ($iri): array => ['href' => $iri], $items),
                $response->toArray()['_links']['items']
            );
        } else {
            $this->assertJsonContains([
                '_embedded' => [
                    'items' => [],
                ],
            ]);
        }
    }

    /**
     * @param ResponseInterface $response     Response from API
     * @param array             $propertyName property name which contains wrong JSON
     */
    protected function assertJsonSchemaError(ResponseInterface $response, string $propertyName) {
        $responseArray = $response->toArray(false);

        $this->assertEquals($propertyName, $responseArray['violations'][0]['propertyPath']);
        $this->assertStringStartsWith('Provided JSON doesn\'t match required schema', $responseArray['violations'][0]['message']);
    }

    /**
     * Validates the number of executed SqlQueries.
     * requieres $client->enableProfiler().
     */
    protected function assertSqlQueryCount(Client $client, int $expected) {
        /** @var DoctrineDataCollector $collector */
        $collector = $client->getProfile()->getCollector('db');

        $this->assertEquals($expected, $collector->getQueryCount());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function assertMatchesEscapedResponseSnapshot(ResponseInterface $response): void {
        $responseArray = $response->toArray(false);
        $escapedArrayValues = self::escapeArrayValues($responseArray);
        $sortedResponseArray = ArrayDeepSort::sort($escapedArrayValues);
        $this->assertMatchesJsonSnapshot($sortedResponseArray);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function assertMatchesResponseSnapshot(ResponseInterface $response): void {
        $responseArray = $response->toArray(false);

        $sortedResponseArray = ArrayDeepSort::sort($responseArray);
        $this->assertMatchesJsonSnapshot($sortedResponseArray);
    }

    private static function escapeValues(mixed &$object): void {
        if (!is_array($object)) {
            $object = 'escaped_value';
        }
    }
}
