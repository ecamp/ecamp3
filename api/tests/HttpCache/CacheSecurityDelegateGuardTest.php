<?php

namespace App\Tests\HttpCache;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use App\HttpCache\CacheMode;
use App\HttpCache\CacheSecurityDelegateResolver;
use App\HttpCache\HttpCacheConfig;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

/**
 * A response that delegates its security to a resource is shared between all users who may read that resource.
 *
 * @internal
 */
class CacheSecurityDelegateGuardTest extends KernelTestCase {
    public function testAllHttpCacheDeclarationsAreValid() {
        foreach ($this->getAllOperations() as $operation) {
            try {
                HttpCacheConfig::fromOperation($operation);
            } catch (\LogicException $e) {
                $this->fail($e->getMessage());
            }
        }
        $this->addToAssertionCount(1);
    }

    /**
     * The expressions can only use the resources of the operation, e.g. not the user: The response must be the same for
     * everyone who may read the delegate.
     */
    public function testDelegateExpressionsOnlyUseVariablesOfTheOperation() {
        $expressionLanguage = new ExpressionLanguage();

        $sharedOperations = $this->getOperationsWithMode(CacheMode::Shared);
        $this->assertNotEmpty($sharedOperations);
        foreach ($sharedOperations as $operation) {
            try {
                $expressionLanguage->parse(HttpCacheConfig::fromOperation($operation)->securityDelegate, CacheSecurityDelegateResolver::getVariableNames($operation));
            } catch (SyntaxError $e) {
                $this->fail("{$operation->getName()}: {$e->getMessage()}");
            }
        }
        $this->addToAssertionCount(1);
    }

    /**
     * Properties with their own security would make the response depend on the user.
     */
    public function testNoPropertyHasItsOwnSecurity() {
        $names = self::getContainer()->get(ResourceNameCollectionFactoryInterface::class)->create();
        $propertyNames = self::getContainer()->get(PropertyNameCollectionFactoryInterface::class);
        $propertyMetadata = self::getContainer()->get(PropertyMetadataFactoryInterface::class);

        $this->assertNotEmpty(iterator_to_array($names));
        foreach ($names as $resourceClass) {
            foreach ($propertyNames->create($resourceClass) as $property) {
                $this->assertNull($propertyMetadata->create($resourceClass, $property)->getSecurity(), "{$resourceClass}::\${$property} has security.");
            }
        }
    }

    public function testCachedOperationsAreReadOperations() {
        foreach ($this->getCachedOperations() as $operation) {
            $this->assertSame('GET', $operation->getMethod(), "{$operation->getName()} must be a GET operation.");
        }
        $this->addToAssertionCount(1);
    }

    public function testSharedCollectionsAreNotFilteredByTheCurrentUser() {
        foreach ($this->getOperationsWithMode(CacheMode::Shared, CacheMode::Public) as $operation) {
            if ($operation instanceof CollectionOperationInterface) {
                $this->assertFalse($operation->getExtraProperties()['filter_by_current_user'] ?? true, "{$operation->getName()} is shared between users, but filters by the current user.");
            }
        }
        $this->addToAssertionCount(1);
    }

    public function testCachedOperations() {
        $cached = [];
        foreach ($this->getCachedOperations() as $operation) {
            $cached[$operation->getUriTemplate()] = HttpCacheConfig::fromOperation($operation)->mode->value;
        }
        ksort($cached);

        $this->assertSame([
            '/activities/{id}{._format}' => 'shared',
            '/camps/{campId}/activities{._format}' => 'shared',
            '/camps/{campId}/categories{._format}' => 'user',
            '/camps/{campId}/checklists{._format}' => 'shared',
            '/content_types/{id}{._format}' => 'public',
            '/content_types{._format}' => 'public',
            '/periods/{periodId}/days{._format}' => 'shared',
            '/periods/{periodId}/schedule_entries{._format}' => 'shared',
        ], $cached);
    }

    public function testPublicOperationsRequireNoSecurity() {
        $publicOperations = $this->getOperationsWithMode(CacheMode::Public);

        $this->assertNotEmpty($publicOperations);
        foreach ($publicOperations as $operation) {
            $this->assertNull($operation->getSecurity(), "{$operation->getName()} is public, but has security.");
        }
    }

    /**
     * @return HttpOperation[]
     */
    private function getCachedOperations(): array {
        return $this->getOperationsWithMode(CacheMode::User, CacheMode::Shared, CacheMode::Public);
    }

    /**
     * @return HttpOperation[]
     */
    private function getOperationsWithMode(CacheMode ...$modes): array {
        $operations = [];
        foreach ($this->getAllOperations() as $operation) {
            $mode = HttpCacheConfig::fromOperation($operation)?->mode;
            if (null !== $mode && in_array($mode, $modes, true)) {
                $operations[] = $operation;
            }
        }

        return $operations;
    }

    /**
     * @return iterable<HttpOperation>
     */
    private function getAllOperations(): iterable {
        $names = self::getContainer()->get(ResourceNameCollectionFactoryInterface::class)->create();
        $factory = self::getContainer()->get(ResourceMetadataCollectionFactoryInterface::class);

        foreach ($names as $resourceClass) {
            foreach ($factory->create($resourceClass) as $resource) {
                yield from $resource->getOperations() ?? [];
            }
        }
    }
}
