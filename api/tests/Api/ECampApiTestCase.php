<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

abstract class ECampApiTestCase extends ApiTestCase {
    use RefreshDatabaseTrait;

    private ?string $token = null;
    private ?IriConverterInterface $iriConverter = null;
    private ?SchemaFactoryInterface $schemaFactory = null;
    private ?ResourceMetadataFactoryInterface $resourceMetadataFactory = null;

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
        // Normally, the database is reset after every request. Since we already need a request to log the user in,
        // we need to disable this behaviour here. This can be removed if this issue is ever resolved:
        // https://github.com/api-platform/api-platform/issues/1668
        $client->disableReboot();
        $client->request('POST', '/authentication_token', ['json' => $credentials ?: [
            'username' => 'test-user',
            'password' => 'test',
        ], 'headers' => ['Content-Type' => 'application/ld+json']]);

        return $client;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected static function createClientWithAdminCredentials(?array $headers = null): Client {
        return static::createClientWithCredentials(['username' => 'admin', 'password' => 'test']);
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

    protected function getExamplePayload(string $resourceClass, array $attributes = [], array $except = []): array {
        $shortName = $this->getResourceMetadataFactory()->create($resourceClass)->getShortName();
        $schema = $this->getSchemaFactory()->buildSchema($resourceClass, 'json', Schema::TYPE_INPUT, 'POST');
        $properties = $schema->getDefinitions()[$shortName]['properties'];
        $writableProperties = array_filter($properties, fn ($property) => !($property['readOnly'] ?? false));
        $writablePropertiesWithExample = array_filter($writableProperties, fn ($property) => ($property['example'] ?? false));

        return array_diff_key(array_merge(array_map(fn ($property) => $property['example'] ?? $property['default'] ?? null, $writablePropertiesWithExample), $attributes), array_flip($except));
    }
}
