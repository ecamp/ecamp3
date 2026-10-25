<?php

namespace App\Tests\HttpCache;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use App\HttpCache\CacheSecurityDelegateListener;
use App\HttpCache\CacheSecurityDelegateResolver;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

/**
 * A response that delegates its security to a resource is shared between all users who may read that resource.
 *
 * @internal
 */
class CacheSecurityDelegateGuardTest extends KernelTestCase {
    /**
     * The expressions can only use the resources of the operation, e.g. not the user: The response must be the same for
     * everyone who may read the delegate.
     */
    public function testDelegateExpressionsOnlyUseVariablesOfTheOperation() {
        $expressionLanguage = new ExpressionLanguage();

        $delegatingOperations = $this->getOperationsWithDelegate();
        $this->assertNotEmpty($delegatingOperations);
        foreach ($delegatingOperations as $operation) {
            $delegate = $operation->getExtraProperties()[CacheSecurityDelegateListener::EXTRA_PROPERTY];
            if (CacheSecurityDelegateListener::DELEGATE_PUBLIC === $delegate) {
                continue;
            }

            try {
                $expressionLanguage->parse($delegate, CacheSecurityDelegateResolver::getVariableNames($operation));
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

    public function testOperationsWithCacheSecurityDelegateAreReadOperations() {
        foreach ($this->getOperationsWithDelegate() as $operation) {
            $this->assertSame('GET', $operation->getMethod(), "{$operation->getName()} must be a GET operation.");
        }
    }

    public function testOperationsDelegatingToPublicRequireNoSecurity() {
        $delegatingOperations = $this->getOperationsWithDelegate(CacheSecurityDelegateListener::DELEGATE_PUBLIC);

        $this->assertNotEmpty($delegatingOperations);
        foreach ($delegatingOperations as $operation) {
            $this->assertNull($operation->getSecurity(), "{$operation->getName()} is public, but has security.");
        }
    }

    /**
     * @return HttpOperation[]
     */
    private function getOperationsWithDelegate(?string $delegate = null): array {
        $operations = [];
        foreach ($this->getAllOperations() as $operation) {
            $operationDelegate = $operation->getExtraProperties()[CacheSecurityDelegateListener::EXTRA_PROPERTY] ?? null;
            if (null !== $operationDelegate && (null === $delegate || $delegate === $operationDelegate)) {
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
