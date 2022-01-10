<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
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
    protected BaseEntity $defaultEntity;
    protected string $entityClass;

    private ?string $token = null;
    private ?IriConverterInterface $iriConverter = null;
    private ?SchemaFactoryInterface $schemaFactory = null;
    private ?ResourceMetadataFactoryInterface $resourceMetadataFactory = null;
    private ?EntityManagerInterface $entityManager = null;

    public function setUp(): void {
        self::bootKernel();
        parent::setUp();
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
                array_diff_key($credentials ?: ['username' => 'test-user'], ['password' => ''])
            )
        ;
        $user = $profile->user;
        $jwtToken = static::getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);
        $lastPeriodPosition = strrpos($jwtToken, '.');
        $jwtHeaderAndPayload = substr($jwtToken, 0, $lastPeriodPosition);
        $jwtSignature = substr($jwtToken, $lastPeriodPosition + 1);

        $cookies = $client->getCookieJar();
        $cookies->set(new Cookie('jwt_hp', $jwtHeaderAndPayload, null, null, 'example.com', false, false, false, 'strict'));
        $cookies->set(new Cookie('jwt_s', $jwtSignature, null, null, 'example.com', false, true, false, 'strict'));

        return $client;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected static function createClientWithAdminCredentials(?array $headers = null): Client {
        return static::createClientWithCredentials(['username' => 'admin']);
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

        return $this->getIriConverter()->getIriFromItem($entityOrFixtureName);
    }

    protected function getSchemaFactory(): SchemaFactoryInterface {
        if (null === $this->schemaFactory) {
            $this->schemaFactory = static::getContainer()->get(SchemaFactoryInterface::class);
        }

        return $this->schemaFactory;
    }

    protected function getResourceMetadataFactory(): ResourceMetadataFactoryInterface {
        if (null === $this->resourceMetadataFactory) {
            $this->resourceMetadataFactory = static::getContainer()->get(ResourceMetadataFactoryInterface::class);
        }

        return $this->resourceMetadataFactory;
    }

    protected function getEntityManager(): EntityManagerInterface {
        if (null === $this->entityManager) {
            $this->entityManager = static::getContainer()->get('doctrine.orm.default_entity_manager');
        }

        return $this->entityManager;
    }

    protected function getExamplePayload(string $resourceClass, string $operationType, string $operationName, array $attributes = [], array $exceptExamples = [], array $exceptAttributes = []): array {
        $schema = $this->getSchemaFactory()->buildSchema($resourceClass, 'json', 'get' === $operationName ? Schema::TYPE_OUTPUT : Schema::TYPE_INPUT, $operationType, $operationName);
        preg_match('/\/([^\/]+)$/', $schema['$ref'], $matches);
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
            $credentials = ['username' => $user->getUsername()];
        }

        $entity ??= $this->defaultEntity;

        static::createClientWithCredentials($credentials)->request('GET', "{$this->endpoint}/".$entity->getId());
    }

    protected function list(?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['username' => $user->getUsername()];
        }

        return static::createClientWithCredentials($credentials)->request('GET', $this->endpoint);
    }

    protected function delete(?BaseEntity $entity = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['username' => $user->getUsername()];
        }

        $entity ??= $this->defaultEntity;

        static::createClientWithCredentials($credentials)->request('DELETE', "{$this->endpoint}/".$entity->getId());
    }

    protected function create(array $payload = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['username' => $user->getUsername()];
        }

        if (null === $payload) {
            $payload = $this->getExampleWritePayload();
        }

        return static::createClientWithCredentials($credentials)->request('POST', $this->endpoint, ['json' => $payload]);
    }

    protected function patch(?BaseEntity $entity = null, array $payload = [], ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['username' => $user->getUsername()];
        }

        $entity ??= $this->defaultEntity;

        return static::createClientWithCredentials($credentials)->request('PATCH', "{$this->endpoint}/".$entity->getId(), [
            'json' => $payload,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
    }

    protected function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            $this->entityClass,
            OperationType::COLLECTION,
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

        // TODO: remove if once PR #2062 is merged
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
        }
    }
}
