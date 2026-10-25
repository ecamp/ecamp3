<?php

namespace App\Tests\HttpCache;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use App\Entity\Camp;
use App\HttpCache\CacheScopeListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A response scoped to a camp is shared between all users who may read that camp.
 * This is only correct if reading the response requires nothing but read access to the camp.
 *
 * @internal
 */
class CacheScopeSecurityTest extends KernelTestCase {
    public function testOperationsScopedToCampRequireExactlyReadAccessToTheCamp() {
        $campReadSecurity = $this->normalize($this->getCampItemGetOperation()->getSecurity());
        $scopedOperations = $this->getOperationsWithCacheScope(CacheScopeListener::SCOPE_CAMP);

        $this->assertNotEmpty($scopedOperations);
        foreach ($scopedOperations as $operation) {
            $this->assertSame(
                $campReadSecurity,
                $this->normalize($this->getSecurityOfCampRelatedRead($operation)),
                "{$operation->getClass()} {$operation->getName()} is scoped to the camp, but its read security differs from the camp's."
            );
        }
    }

    public function testOperationsWithCacheScopeAreReadOperations() {
        foreach ($this->getOperationsWithCacheScope() as $operation) {
            $this->assertSame('GET', $operation->getMethod(), "{$operation->getName()} must be a GET operation.");
        }
    }

    public function testOperationsScopedToPublicRequireNoSecurity() {
        $scopedOperations = $this->getOperationsWithCacheScope(CacheScopeListener::SCOPE_PUBLIC);

        $this->assertNotEmpty($scopedOperations);
        foreach ($scopedOperations as $operation) {
            $this->assertNull($operation->getSecurity(), "{$operation->getName()} is public, but has security.");
        }
    }

    /**
     * @return HttpOperation[]
     */
    private function getOperationsWithCacheScope(?string $scope = null): array {
        $operations = [];
        foreach ($this->getAllOperations() as $operation) {
            $operationScope = $operation->getExtraProperties()[CacheScopeListener::EXTRA_PROPERTY] ?? null;
            if (null !== $operationScope && (null === $scope || $scope === $operationScope)) {
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
