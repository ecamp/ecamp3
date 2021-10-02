<?php

namespace App\Metadata\Resource\Factory;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

class GraphQLSecurityResourceMetadataFactory implements ResourceMetadataFactoryInterface {
    public function __construct(private ResourceMetadataFactoryInterface $decorated) {
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass): ResourceMetadata {
        $resourceMetadata = $this->decorated->create($resourceClass);

        return $resourceMetadata->withGraphql($this->copyRestSecurityToGraphQL($resourceMetadata));
    }

    private function copyRestSecurityToGraphQL(ResourceMetadata $metadata): array {
        $result = $metadata->getGraphql();

        $collectionOperationMapping = ['get' => 'collection_query', 'post' => 'create'];
        foreach ($metadata->getCollectionOperations() as $operation => $operationMetadata) {
            $graphQLOperation = $collectionOperationMapping[$operation] ?? $operation;
            foreach (['security', 'security_post_denormalize'] as $security) {
                if (false !== ($restSecurity = $operationMetadata[$security] ?? false)) {
                    $result[$graphQLOperation][$security] = $result[$graphQLOperation][$security] ?? $restSecurity;
                }
            }
        }

        $itemOperationMapping = ['get' => 'item_query', 'patch' => 'update'];
        foreach ($metadata->getItemOperations() as $operation => $operationMetadata) {
            $graphQLOperation = $itemOperationMapping[$operation] ?? $operation;
            foreach (['security', 'security_post_denormalize'] as $security) {
                if (false !== ($restSecurity = $operationMetadata[$security] ?? false)) {
                    $result[$graphQLOperation][$security] = $result[$graphQLOperation][$security] ?? $restSecurity;
                }
            }
        }

        return $result;
    }
}
