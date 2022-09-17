<?php

namespace App\Metadata\Resource\Factory;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Exception\OperationNotFoundException;
use ApiPlatform\Exception\ResourceClassNotFoundException;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ApiPlatform\State\Pagination\PaginationOptions;
use Psr\Container\ContainerInterface;

class UriTemplateFactory {
    protected ?array $resourceNameMapping = [];

    public function __construct(
        private ContainerInterface $filterLocator,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        private IriConverterInterface $iriConverter,
        private PaginationOptions $paginationOptions,
    ) {
        foreach ($resourceNameCollectionFactory->create() as $className) {
            $shortName = $this->resourceMetadataCollectionFactory->create($className)->getOperation()->getShortName();
            $this->resourceNameMapping[lcfirst($shortName)] = $className;
        }
    }

    /**
     * Create an URI template based on the allowed parameters for the specified entity.
     *
     * @return array contains the templated URI (or null when not successful), as well as a boolean flag which
     *               indicates whether any template parameters are present in the URI
     *
     * @throws ResourceClassNotFoundException
     */
    public function createFromShortname(string $shortName): array {
        $resourceClass = $this->resourceNameMapping[lcfirst($shortName)] ?? null;

        if (!$resourceClass) {
            return [null, false];
        }

        return $this->createFromResourceClass($resourceClass);
    }

    /**
     * Create an URI template based on the allowed parameters for the specified entity.
     *
     * @return array contains the templated URI (or null when not successful), as well as a boolean flag which
     *               indicates whether any template parameters are present in the URI
     *
     * @throws ResourceClassNotFoundException
     */
    public function createFromResourceClass(string $resourceClass): array {
        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);
        $getCollectionOperation = $resourceMetadataCollection->getOperation(null, true, true);

        $baseUri = $this->iriConverter->getIriFromResource($resourceClass, UrlGeneratorInterface::ABS_PATH, $getCollectionOperation);
        $idParameter = $this->getIdParameter($resourceMetadataCollection);
        $queryParameters = $this->getQueryParameters($resourceClass, $resourceMetadataCollection);
        $additionalPathParameter = $this->allowsActionParameter($resourceMetadataCollection) ? '{/action}' : '';

        return [
            $baseUri.$idParameter.$additionalPathParameter.$queryParameters,
            // The link is templated as soon as either idParameter or queryParameters is not empty
            $idParameter || $queryParameters || $additionalPathParameter,
        ];
    }

    /**
     * Returns an optional /id URL parameter, if access to single items is allowed.
     */
    protected function getIdParameter(ResourceMetadataCollection $resourceMetadataCollection): string {
        $getSingleItemIsAllowed = true;

        try {
            $resourceMetadataCollection->getOperation(null, false, true);
        } catch (OperationNotFoundException) {
            $getSingleItemIsAllowed = false;
        }

        return $getSingleItemIsAllowed ? '{/id}' : '';
    }

    /**
     * Collects all the query parameters and combines them into an optional URI parameter.
     */
    protected function getQueryParameters(string $resourceClass, ResourceMetadataCollection $resourceMetadataCollection): string {
        $parameters = array_merge($this->getFilterParameters($resourceClass, $resourceMetadataCollection), $this->getPaginationParameters($resourceMetadataCollection));

        return empty($parameters) ? '' : '{?'.implode(',', $parameters).'}';
    }

    /**
     * Gets parameters corresponding to enabled filters.
     * Based on API Platform's OpenApiFactory::getFilterParameters.
     */
    protected function getFilterParameters(string $resourceClass, ResourceMetadataCollection $resourceMetadataCollection) {
        $parameters = [];

        $resourceFilters = $resourceMetadataCollection->getOperation(null, true)->getFilters();
        foreach ($resourceFilters as $filterId) {
            if (!$filter = $this->filterLocator->get($filterId)) {
                continue;
            }

            foreach ($filter->getDescription($resourceClass) as $name => $data) {
                $parameters[] = $name;
            }
        }

        return $parameters;
    }

    /**
     * Gets parameters corresponding to enabled pagination parameters.
     * Based on API Platform's OpenApiFactory::getPaginationParameters.
     */
    protected function getPaginationParameters(ResourceMetadataCollection $resourceMetadataCollection) {
        if (!$this->paginationOptions->isPaginationEnabled()) {
            return [];
        }

        $parameters = [];
        $operation = $resourceMetadataCollection->getOperation('get', true);

        if ($operation->getPaginationEnabled() ?? $this->paginationOptions->isPaginationEnabled()) {
            $parameters[] = $this->paginationOptions->getPaginationPageParameterName();

            if ($operation->getPaginationClientItemsPerPage() ?? $this->paginationOptions->getClientItemsPerPage()) {
                $parameters[] = $this->paginationOptions->getItemsPerPageParameterName();
            }
        }

        if ($operation->getPaginationClientEnabled() ?? $this->paginationOptions->getPaginationClientEnabled()) {
            $parameters[] = $this->paginationOptions->getPaginationClientEnabledParameterName();
        }

        return $parameters;
    }

    protected function allowsActionParameter(ResourceMetadataCollection $resourceMetadataCollection): bool {
        foreach ($resourceMetadataCollection->getIterator()->current()->getOperations() as $operation) {
            /*
             * Matches:
             * {/inviteKey}/find
             * users{/id}/activate
             *
             * Does not match:
             * profiles{/id}
             */
            if ($operation instanceof HttpOperation) {
                if (preg_match('/^.*\\/?{.*}\\/.+$/', $operation->getUriTemplate() ?? '')) {
                    return true;
                }
            }
        }

        return false;
    }
}
