<?php

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\Util\OperationRequestInitiatorTrait;
use ApiPlatform\State\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Request;

/**
 * Finds the cache scope that the operation of a request declares, if any.
 */
final class CacheScopeResolver {
    use OperationRequestInitiatorTrait;

    private const string ENTRYPOINT_ROUTE = 'api_entrypoint';

    public function __construct(ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory) {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    /**
     * @return null|CacheScopeListener::SCOPE_*
     */
    public function getDeclaredScope(Request $request): ?string {
        if (self::ENTRYPOINT_ROUTE === $request->attributes->get('_route')) {
            return CacheScopeListener::SCOPE_PUBLIC;
        }

        $operation = $this->getOperation($request);
        if (!$operation) {
            return null;
        }

        return match ($operation->getExtraProperties()[CacheScopeListener::EXTRA_PROPERTY] ?? null) {
            CacheScopeListener::SCOPE_PUBLIC => CacheScopeListener::SCOPE_PUBLIC,
            CacheScopeListener::SCOPE_CAMP => CacheScopeListener::SCOPE_CAMP,
            default => null,
        };
    }

    public function getOperation(Request $request): ?Operation {
        if (!RequestAttributesExtractor::extractAttributes($request)) {
            return null;
        }

        return $this->initializeOperation($request);
    }
}
