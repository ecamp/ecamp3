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
    private ?Client $clientWithCredentials = null;
    private ?IriConverterInterface $iriConverter = null;
    private ?SchemaFactoryInterface $schemaFactory = null;
    private ?ResourceMetadataFactoryInterface $resourceMetadataFactory = null;

    public function setUp(): void {
        self::bootKernel();
        parent::setUp();
    }

    /**
     * @param null $token
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function createClientWithCredentials($token = null): Client {
        $token = $token ?: $this->getToken();

        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token, 'accept' => 'application/hal+json']]);
    }

    /**
     * @param null $token
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function createClientWithAdminCredentials($token = null): Client {
        $token = $token ?: $this->getToken(['username' => 'admin', 'password' => 'test']);

        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token, 'accept' => 'application/hal+json']]);
    }

    /**
     * Use other credentials if needed.
     *
     * @param array $body
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getToken($body = []): string {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient()->request('POST', '/authentication_token', ['json' => $body ?: [
            'username' => 'test-user',
            'password' => 'test',
        ]]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());
        $this->token = $data->token;

        return $this->token;
    }

    protected function getIriConverter(): IriConverterInterface {
        if (null === $this->iriConverter) {
            $this->iriConverter = static::$container->get(IriConverterInterface::class);
        }

        return $this->iriConverter;
    }

    protected function getSchemaFactory(): SchemaFactoryInterface {
        if (null === $this->schemaFactory) {
            $this->schemaFactory = static::$container->get(SchemaFactoryInterface::class);
        }

        return $this->schemaFactory;
    }

    protected function getResourceMetadataFactory(): ResourceMetadataFactoryInterface {
        if (null === $this->resourceMetadataFactory) {
            $this->resourceMetadataFactory = static::$container->get(ResourceMetadataFactoryInterface::class);
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
