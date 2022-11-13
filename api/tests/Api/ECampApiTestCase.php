<?php

namespace App\Tests\Api;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\BaseEntity;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class ECampApiTestCase extends ApiTestCase {
    use RefreshDatabaseTrait;

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

    protected function getExamplePayload(string $resourceClass, string $endpoint, string $operationName = 'get', array $attributes = [], array $exceptExamples = [], array $exceptAttributes = []): array {
        // At the moment, specific operations can only be found via route name
        // --> build route name based on required operation and resource endpoint
        $fullOperationName = '';

        switch ($operationName) {
            case 'get':
            case 'patch':
            case 'put':
                $fullOperationName = "_api_{$endpoint}/{id}{._format}_";

                break;

            case 'post':
            case 'get_collection':
                $fullOperationName = "_api_{$endpoint}{._format}_";

                break;

            default:
                throw new \Exception("invalid \$operationName {$operationName}");
        }
        $fullOperationName .= $operationName;

        $resourceMetadataCollection = $this->getResourceMetadataFactory()->create($resourceClass);
        $operation = $resourceMetadataCollection->getOperation($fullOperationName);

        // build JSON schema based on requested operation
        $schema = $this->getSchemaFactory()->buildSchema($resourceClass, 'json', 'get' === substr($operationName, 0, 3) ? Schema::TYPE_OUTPUT : Schema::TYPE_INPUT, $operation);

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
    }

    protected function get(?BaseEntity $entity = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        $entity ??= $this->defaultEntity;

        return static::createClientWithCredentials($credentials)->request('GET', $this->routePrefix.$this->endpoint.'/'.$entity->getId());
    }

    protected function list(?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        return static::createClientWithCredentials($credentials)->request('GET', $this->routePrefix.$this->endpoint);
    }

    protected function delete(?BaseEntity $entity = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        $entity ??= $this->defaultEntity;

        return static::createClientWithCredentials($credentials)->request('DELETE', $this->routePrefix.$this->endpoint.'/'.$entity->getId());
    }

    protected function create(array $payload = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        if (null === $payload) {
            $payload = $this->getExampleWritePayload();
        }

        return static::createClientWithCredentials($credentials)->request('POST', $this->routePrefix.$this->endpoint, ['json' => $payload]);
    }

    protected function patch(?BaseEntity $entity = null, array $payload = [], ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['email' => $user->getEmail()];
        }

        $entity ??= $this->defaultEntity;

        return static::createClientWithCredentials($credentials)->request('PATCH', $this->routePrefix.$this->endpoint.'/'.$entity->getId(), [
            'json' => $payload,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
    }

    protected function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            $this->entityClass,
            $this->endpoint,
            'post',
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
}
