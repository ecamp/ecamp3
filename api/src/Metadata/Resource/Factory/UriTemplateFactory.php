<?php

namespace App\Metadata\Resource\Factory;

use ApiPlatform\Metadata\Exception\ResourceClassNotFoundException;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use ApiPlatform\State\Pagination\PaginationOptions;
use App\Metadata\Resource\OperationHelper;
use Psr\Container\ContainerInterface;

/**
 * Given an entity class, creates an URI template (link with placeholders) for accessing this type of entity in the API.
 * Format follows RFC6570 (https://datatracker.ietf.org/doc/html/rfc6570).
 *
 * Currently, multiple ApiResources per Class ist not implemented (refactoring needed to support this use-case)
 */
class UriTemplateFactory {
    protected ?array $resourceNameMapping = [];

    public function __construct(
        private readonly ContainerInterface $filterLocator,
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        private readonly IriConverterInterface $iriConverter,
        private readonly PaginationOptions $paginationOptions,
    ) {
        foreach ($resourceNameCollectionFactory->create() as $className) {
            $shortName = $this->resourceMetadataCollectionFactory->create($className)[0]->getShortName();
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
        $getCollectionOperation = OperationHelper::findOneByType($resourceMetadataCollection, GetCollection::class);

        $baseUri = $this->iriConverter->getIriFromResource($resourceClass, UrlGeneratorInterface::ABS_PATH, $getCollectionOperation);
        $relativeUri = $this->iriConverter->getIriFromResource($resourceClass, UrlGeneratorInterface::REL_PATH, $getCollectionOperation);

        $idParameter = $this->getIdParameter($resourceMetadataCollection);
        $queryParameters = $this->getQueryParameters($resourceClass, $resourceMetadataCollection);
        $additionalPathParameter = $this->allowsActionParameter($relativeUri, $resourceMetadataCollection) ? '{/action}' : '';

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
        if (OperationHelper::findOneByType($resourceMetadataCollection, Get::class)) {
            return '{/id}';
        }

        return '';
    }

    /**
     * Collects all the query parameters and combines them into an optional URI parameter.
     */
    protected function getQueryParameters(string $resourceClass, ResourceMetadataCollection $resourceMetadataCollection): string {
        $parameters = array_merge($this->getFilterParameters($resourceClass, $resourceMetadataCollection), $this->getPaginationParameters($resourceMetadataCollection));

        return [] === $parameters ? '' : '{?'.join(',', $parameters).'}';
    }

    /**
     * Gets parameters corresponding to enabled filters.
     * Based on API Platform's OpenApiFactory::getFilterParameters.
     */
    protected function getFilterParameters(string $resourceClass, ResourceMetadataCollection $resourceMetadataCollection) {
        $parameters = [];
        $operation = OperationHelper::findOneByType($resourceMetadataCollection, GetCollection::class);

        foreach ($operation?->getParameters() ?? [] as $key => $parameter) {
            if ($parameter instanceof QueryParameter) {
                array_push($parameters, ...$this->getParameterNames($key, $parameter));
            }
        }

        foreach ($operation?->getFilters() ?? [] as $filterId) {
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
        $operation = OperationHelper::findOneByType($resourceMetadataCollection, GetCollection::class);

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

    protected function allowsActionParameter(string $uriTemplateBase, ResourceMetadataCollection $resourceMetadataCollection): bool {
        foreach ($resourceMetadataCollection->getIterator()->current()->getOperations() as $operation) {
            /*
             * Matches:
             * - invitations/{/inviteKey}/find
             * - /users{/id}/activate
             *
             * Does not match:
             * - profiles{/id}
             * - any uriTemplate, which doesn't start with the same $uriTemplateBase
             */
            if ($operation instanceof HttpOperation) {
                if (preg_match('/^\/?'.preg_quote($uriTemplateBase, '/').'.*\/?{.*}\/.+$/', $operation->getUriTemplate() ?? '')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Filters which document several query parameters (e.g. the before/after variants of date filters)
     * contribute all of them, all other filters contribute the parameter key.
     *
     * @return string[]
     */
    private function getParameterNames(string $key, QueryParameter $parameter): array {
        $filter = $parameter->getFilter();
        if (\is_string($filter) && $this->filterLocator->has($filter)) {
            $filter = $this->filterLocator->get($filter);
        }

        if (!$filter instanceof OpenApiParameterFilterInterface) {
            return [$key];
        }

        $openApiParameters = $filter->getOpenApiParameters($parameter->withKey($key));
        $names = array_map(
            fn (OpenApiParameter $openApiParameter) => $openApiParameter->getName(),
            \is_array($openApiParameters) ? $openApiParameters : array_filter([$openApiParameters])
        );

        return [] === $names ? [$key] : $names;
    }
}
