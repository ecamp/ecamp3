<?php

namespace App\Metadata\Resource\Factory;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\PaginationOptions;
use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use Psr\Container\ContainerInterface;

class UriTemplateFactory {
    protected ?array $resourceNameMapping = [];

    public function __construct(
        private ContainerInterface $filterLocator,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory,
        ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        private IriConverterInterface $iriConverter,
        private PaginationOptions $paginationOptions,
    ) {
        foreach ($resourceNameCollectionFactory->create() as $className) {
            $shortName = $this->resourceMetadataFactory->create($className)->getShortName();
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
        $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);

        $baseUri = $this->iriConverter->getIriFromResourceClass($resourceClass);
        $idParameter = $this->getIdParameter($resourceMetadata);
        $queryParameters = $this->getQueryParameters($resourceClass, $resourceMetadata);
        $additionalPathParameter = $this->allowsActionParameter($resourceMetadata) ? '{/action}' : '';

        return [
            $baseUri.$idParameter.$additionalPathParameter.$queryParameters,
            // The link is templated as soon as either idParameter or queryParameters is not empty
            $idParameter || $queryParameters || $additionalPathParameter,
        ];
    }

    /**
     * Returns an optional /id URL parameter, if access to single items is allowed.
     */
    protected function getIdParameter(ResourceMetadata $resourceMetadata): string {
        $getSingleItemIsAllowed = array_key_exists('get', $resourceMetadata->getItemOperations())
            && (NotFoundAction::class !== ($resourceMetadata->getItemOperations()['get']['controller'] ?? ''));

        return $getSingleItemIsAllowed ? '{/id}' : '';
    }

    /**
     * Collects all the query parameters and combines them into an optional URI parameter.
     */
    protected function getQueryParameters(string $resourceClass, ResourceMetadata $resourceMetadata): string {
        $parameters = array_merge($this->getFilterParameters($resourceClass, $resourceMetadata), $this->getPaginationParameters($resourceMetadata));

        return empty($parameters) ? '' : '{?'.implode(',', $parameters).'}';
    }

    /**
     * Gets parameters corresponding to enabled filters.
     * Based on API Platform's OpenApiFactory::getFilterParameters.
     */
    protected function getFilterParameters(string $resourceClass, ResourceMetadata $resourceMetadata) {
        $parameters = [];

        $resourceFilters = $resourceMetadata->getCollectionOperationAttribute('get', 'filters', [], true);
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
    protected function getPaginationParameters(ResourceMetadata $resourceMetadata) {
        if (!$this->paginationOptions->isPaginationEnabled()) {
            return [];
        }

        $parameters = [];

        if ($resourceMetadata->getCollectionOperationAttribute('get', 'pagination_enabled', true, true)) {
            $parameters[] = $this->paginationOptions->getPaginationPageParameterName();

            if ($resourceMetadata->getCollectionOperationAttribute('get', 'pagination_client_items_per_page', $this->paginationOptions->getClientItemsPerPage(), true)) {
                $parameters[] = $this->paginationOptions->getItemsPerPageParameterName();
            }
        }

        if ($resourceMetadata->getCollectionOperationAttribute('get', 'pagination_client_enabled', $this->paginationOptions->getPaginationClientEnabled(), true)) {
            $parameters[] = $this->paginationOptions->getPaginationClientEnabledParameterName();
        }

        return $parameters;
    }

    protected function allowsActionParameter(ResourceMetadata $resourceMetadata): bool {
        foreach ($resourceMetadata->getItemOperations() as $itemOperation) {
            /*
             * Matches:
             * {/inviteKey}/find
             * users{/id}/activate
             *
             * Does not match:
             * profiles{/id}
             */
            if (preg_match('/^.*\\/?{.*}\\/.+$/', $itemOperation['path'] ?? '')) {
                return true;
            }
        }

        return false;
    }
}
