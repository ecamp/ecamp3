<?php

namespace App\Tests\HttpCache;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use App\Entity\Camp;
use App\HttpCache\CacheSecurityDelegateListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A response that delegates its security to a camp is shared between all users who may read that camp.
 * This is only correct if reading the response requires nothing but read access to the camp.
 *
 * @internal
 */
class CacheSecurityDelegateGuardTest extends KernelTestCase {
    public function testOperationsDelegatingToCampRequireExactlyReadAccessToTheCamp() {
        $campReadSecurity = $this->normalize($this->getCampItemGetOperation()->getSecurity());
        $delegatingOperations = $this->getOperationsWithDelegate(CacheSecurityDelegateListener::DELEGATE_CAMP);

        $this->assertNotEmpty($delegatingOperations);
        foreach ($delegatingOperations as $operation) {
            $this->assertSame(
                $campReadSecurity,
                $this->normalize($this->getSecurityOfCampRelatedRead($operation)),
                "{$operation->getClass()} {$operation->getName()} delegates its security to the camp, but its read security differs from the camp's."
            );
        }
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

    private function getCampItemGetOperation(): HttpOperation {
        foreach ($this->getAllOperations() as $operation) {
            if (Camp::class === $operation->getClass() && 'GET' === $operation->getMethod() && !$operation instanceof CollectionOperationInterface) {
                return $operation;
            }
        }

        throw new \LogicException('No item GET operation found for Camp');
    }

    /**
     * The security of an item operation is on the operation itself, the security of
     * a subresource collection is on the link to the parent resource.
     */
    private function getSecurityOfCampRelatedRead(HttpOperation $operation): ?string {
        if (!$operation instanceof CollectionOperationInterface) {
            return $operation->getSecurity();
        }

        $links = array_filter($operation->getUriVariables() ?? [], fn ($link) => $link instanceof Link && null !== $link->getSecurity());
        $this->assertCount(1, $links, "{$operation->getName()} must have exactly one parent link with security.");

        return array_values($links)[0]->getSecurity();
    }

    private function normalize(?string $security): ?string {
        if (null === $security) {
            return null;
        }

        $security = preg_replace('/\s+/', ' ', trim($security));

        // the parent is named differently in each subresource ("camp", "period", "object")
        return preg_replace('/, (object|camp|period)\)/', ', X)', $security);
    }
}
