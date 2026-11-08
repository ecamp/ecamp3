<?php

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\Util\OperationRequestInitiatorTrait;
use ApiPlatform\State\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Request;

final class HttpCacheConfigResolver {
    use OperationRequestInitiatorTrait;

    private const string ENTRYPOINT_ROUTE = 'api_entrypoint';

    public function __construct(ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory) {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    public function getConfig(Request $request): ?HttpCacheConfig {
        if (self::ENTRYPOINT_ROUTE === $request->attributes->get('_route')) {
            return HttpCacheConfig::public();
        }

        $operation = $this->getOperation($request);

        return null === $operation ? null : HttpCacheConfig::fromOperation($operation);
    }

    public function isCached(Request $request): bool {
        return null !== $this->getConfig($request);
    }

    public function getOperation(Request $request): ?Operation {
        if (!RequestAttributesExtractor::extractAttributes($request)) {
            return null;
        }

        return $this->initializeOperation($request);
    }
}
