<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\JsonSchema\Schema;
use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\BrowserKit\Cookie;
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

        /** @var User $user */
        $user = static::$container->get(UserRepository::class)->findBy(array_diff_key($credentials ?: ['username' => 'test-user'], ['password' => '']));
        $jwtToken = static::$container->get('lexik_jwt_authentication.jwt_manager')->create($user[0]);
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
        return static::createClientWithCredentials(['username' => 'admin', 'password' => 'test']);
    }

    protected static function createBasicClient(?array $headers = null): Client {
        return static::createClient([], ['headers' => $headers ?: ['accept' => 'application/hal+json', 'content-type' => 'application/ld+json']]);
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
